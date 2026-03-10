<?php

namespace App\Console\Commands;

use App\Enums\CourseStateEnums;
use App\Enums\CourseTypeEnums;
use App\Enums\UserRoleEnums;
use App\Models\Course;
use App\Models\Exercise;
use App\Models\Lesson;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class RefreshRealLifeLearningDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'learning:refresh-real-data
                            {--force-all : Update all courses/lessons/exercises, not only generic sample titles}
                            {--dry-run : Show what would be updated without writing to DB}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replace generic sample learning data with realistic subcategory-based course, lesson, and exercise content.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $forceAll = (bool) $this->option('force-all');

        $teacherOrAdminIds = User::query()
            ->whereIn('role', [UserRoleEnums::ADMIN->value, UserRoleEnums::TEACHER->value])
            ->pluck('id');

        if ($teacherOrAdminIds->isEmpty()) {
            $this->error('No admin/teacher users found. Cannot assign creators for missing lessons.');

            return self::FAILURE;
        }

        $courses = Course::query()
            ->with([
                'sub_category:id,name',
                'lessons' => fn ($query) => $query->with(['exercises' => fn ($exerciseQuery) => $exerciseQuery->orderBy('id')])->orderBy('id'),
            ])
            ->orderBy('id')
            ->get();

        if ($courses->isEmpty()) {
            $this->warn('No courses found in DB.');

            return self::SUCCESS;
        }

        $updatedCourses = 0;
        $createdCourses = 0;
        $updatedLessons = 0;
        $createdLessons = 0;
        $updatedExercises = 0;
        $createdExercises = 0;
        $subCategoryUsageCounters = [];

        foreach ($courses as $course) {
            $subcategoryName = trim((string) optional($course->sub_category)->name);
            if ($subcategoryName === '') {
                $subcategoryName = 'General Studies';
            }

            $subCategoryUsageCounters[$subcategoryName] = ($subCategoryUsageCounters[$subcategoryName] ?? 0) + 1;
            $profile = $this->profileForSubCategory($subcategoryName);
            $profileSlot = $subCategoryUsageCounters[$subcategoryName] - 1;

            if ($forceAll || $this->isGenericCourseTitle((string) $course->title)) {
                $newTitle = $profile['course_titles'][$profileSlot % count($profile['course_titles'])];
                $newDescription = $this->courseDescription($subcategoryName, $newTitle);
                $newType = $profile['course_type'];
                $newFee = (int) ($profile['fees'][$profileSlot % count($profile['fees'])] ?? 20000);

                if (! $dryRun) {
                    $course->title = $newTitle;
                    $course->description = $newDescription;
                    $course->courseType = $newType;
                    $course->fees = $newFee;
                    $course->save();
                }

                $updatedCourses++;
            }

            $lessonTitles = $this->rotateAndTake($profile['lesson_titles'], $profileSlot, 4);
            $existingLessons = $course->lessons instanceof Collection ? $course->lessons : collect();
            $eligibleLessons = $existingLessons->filter(function (Lesson $lesson) use ($forceAll): bool {
                return $forceAll || $this->isGenericLessonTitle((string) $lesson->title);
            })->values();

            foreach ($eligibleLessons as $lessonIndex => $lesson) {
                $lessonTitle = $lessonTitles[$lessonIndex % count($lessonTitles)];
                $lessonBody = $this->lessonBody($subcategoryName, (string) $course->title, $lessonTitle, $lessonIndex + 1);

                if (! $dryRun) {
                    $lesson->title = $lessonTitle;
                    $lesson->body = $lessonBody;
                    $lesson->save();
                }

                $updatedLessons++;
            }

            if ($existingLessons->count() < 3) {
                $missingLessonCount = 3 - $existingLessons->count();
                for ($i = 0; $i < $missingLessonCount; $i++) {
                    $newLessonTitle = $lessonTitles[($existingLessons->count() + $i) % count($lessonTitles)];
                    $newLessonBody = $this->lessonBody($subcategoryName, (string) $course->title, $newLessonTitle, $existingLessons->count() + $i + 1);

                    if (! $dryRun) {
                        Lesson::query()->create([
                            'title' => $newLessonTitle,
                            'body' => $newLessonBody,
                            'creator_id' => (int) ($course->createdUser_id ?: $teacherOrAdminIds->first()),
                            'course_id' => $course->id,
                        ]);
                    }

                    $createdLessons++;
                }
            }

            $course->load(['lessons' => fn ($query) => $query->with(['exercises' => fn ($exerciseQuery) => $exerciseQuery->orderBy('id')])->orderBy('id')]);
            $exercisePrompts = $profile['exercise_prompts'];

            foreach ($course->lessons as $lessonPosition => $lesson) {
                $eligibleExercises = $lesson->exercises->filter(function (Exercise $exercise) use ($forceAll): bool {
                    return $forceAll || $this->isGenericExerciseTitle((string) $exercise->title);
                })->values();

                foreach ($eligibleExercises as $exerciseIndex => $exercise) {
                    $exerciseNumber = $exerciseIndex + 1;
                    $content = $exercisePrompts[($lessonPosition + $exerciseIndex) % count($exercisePrompts)];
                    $content = $content.' (Lesson focus: '.$lesson->title.')';

                    if (! $dryRun) {
                        $exercise->title = 'Exercise '.$exerciseNumber;
                        $exercise->content = $content;
                        $exercise->save();
                    }

                    $updatedExercises++;
                }

                if ($lesson->exercises->count() < 2) {
                    $missingExerciseCount = 2 - $lesson->exercises->count();
                    for ($i = 0; $i < $missingExerciseCount; $i++) {
                        $exerciseNumber = $lesson->exercises->count() + $i + 1;
                        $content = $exercisePrompts[($lessonPosition + $i) % count($exercisePrompts)];
                        $content = $content.' (Lesson focus: '.$lesson->title.')';

                        if (! $dryRun) {
                            Exercise::query()->create([
                                'title' => 'Exercise '.$exerciseNumber,
                                'content' => $content,
                                'lesson_id' => $lesson->id,
                            ]);
                        }

                        $createdExercises++;
                    }
                }
            }
        }

        $adminId = User::query()
            ->where('role', UserRoleEnums::ADMIN->value)
            ->value('id');
        $subcategoryIdsWithCourses = Course::query()->pluck('sub_category_id')->unique()->values();
        $allSubcategories = SubCategory::query()->orderBy('id')->get(['id', 'name']);
        $creatorCursor = 0;
        $creatorIds = $teacherOrAdminIds->values();

        foreach ($allSubcategories as $subcategory) {
            if ($subcategoryIdsWithCourses->contains((int) $subcategory->id)) {
                continue;
            }

            $subcategoryName = trim((string) $subcategory->name);
            $profile = $this->profileForSubCategory($subcategoryName);
            $creatorId = (int) ($creatorIds[$creatorCursor % $creatorIds->count()] ?? $creatorIds->first());
            $creatorCursor++;

            $newCoursePayload = [
                'title' => $profile['course_titles'][0],
                'description' => $this->courseDescription($subcategoryName, $profile['course_titles'][0]),
                'image' => 'course/sample.jpg',
                'courseType' => $profile['course_type'] ?? CourseTypeEnums::BASIC->value,
                'fees' => (int) ($profile['fees'][0] ?? 25000),
                'state' => CourseStateEnums::APPROVED->value,
                'createdUser_id' => $creatorId,
                'approvedUser_id' => $adminId ?: $creatorId,
                'sub_category_id' => (int) $subcategory->id,
            ];

            if (! $dryRun) {
                $newCourse = Course::query()->create($newCoursePayload);
                $lessonTitles = $this->rotateAndTake($profile['lesson_titles'], 0, 3);
                $exercisePrompts = $profile['exercise_prompts'];

                foreach ($lessonTitles as $lessonIndex => $lessonTitle) {
                    $lesson = Lesson::query()->create([
                        'title' => $lessonTitle,
                        'body' => $this->lessonBody($subcategoryName, $newCourse->title, $lessonTitle, $lessonIndex + 1),
                        'creator_id' => $creatorId,
                        'course_id' => $newCourse->id,
                    ]);
                    $createdLessons++;

                    for ($exerciseIndex = 0; $exerciseIndex < 2; $exerciseIndex++) {
                        Exercise::query()->create([
                            'title' => 'Exercise '.($exerciseIndex + 1),
                            'content' => $exercisePrompts[($lessonIndex + $exerciseIndex) % count($exercisePrompts)].' (Lesson focus: '.$lessonTitle.')',
                            'lesson_id' => $lesson->id,
                        ]);
                        $createdExercises++;
                    }
                }
            } else {
                $createdLessons += 3;
                $createdExercises += 6;
            }

            $createdCourses++;
        }

        $mode = $dryRun ? 'DRY-RUN' : 'APPLIED';
        $this->info("[{$mode}] Real-life learning data refresh completed.");
        $this->line('Updated courses: '.$updatedCourses);
        $this->line('Created courses: '.$createdCourses);
        $this->line('Updated lessons: '.$updatedLessons);
        $this->line('Created lessons: '.$createdLessons);
        $this->line('Updated exercises: '.$updatedExercises);
        $this->line('Created exercises: '.$createdExercises);

        return self::SUCCESS;
    }

    /**
     * @return array{
     *  course_titles: array<int, string>,
     *  lesson_titles: array<int, string>,
     *  exercise_prompts: array<int, string>,
     *  course_type: string,
     *  fees: array<int, int>
     * }
     */
    private function profileForSubCategory(string $subCategoryName): array
    {
        $profiles = [
            'Web Development' => [
                'course_titles' => ['Full-Stack Web Development Bootcamp', 'Modern Laravel for Production Apps', 'Frontend to Backend Web Engineer Path'],
                'lesson_titles' => ['Developer Setup and Git Workflow', 'Responsive UI with Semantic HTML/CSS', 'RESTful APIs and Authentication', 'Deploying and Monitoring Web Apps', 'Database Modeling for Web Projects'],
                'exercise_prompts' => ['Build a landing page from a design brief', 'Create a CRUD API with validation and pagination', 'Deploy your app and document rollback steps', 'Implement secure user login and profile update flow'],
                'course_type' => CourseTypeEnums::ADVANCED->value,
                'fees' => [45000, 50000, 55000],
            ],
            'Mobile App Development' => [
                'course_titles' => ['Mobile App Development with Flutter', 'Android and iOS Product Build Journey', 'From Idea to Store: Mobile App Delivery'],
                'lesson_titles' => ['Mobile UX Patterns and Navigation', 'State Management for Scalable Apps', 'API Integration and Offline Handling', 'App Release Checklist and Monitoring', 'Performance Tuning for Mid-Range Devices'],
                'exercise_prompts' => ['Build a task tracker app screen and navigation', 'Integrate API data with loading and error states', 'Implement offline cache and retry logic', 'Prepare release notes and QA checklist for store submission'],
                'course_type' => CourseTypeEnums::ADVANCED->value,
                'fees' => [42000, 47000, 52000],
            ],
            'Data Science & Analytics' => [
                'course_titles' => ['Practical Data Analytics for Business', 'Data Science Foundations with Real Datasets', 'Data Storytelling and Decision Insights'],
                'lesson_titles' => ['Data Cleaning and Quality Checks', 'Exploratory Analysis and Visualization', 'Feature Engineering Basics', 'Model Evaluation for Real Use Cases', 'Presenting Insights to Stakeholders'],
                'exercise_prompts' => ['Clean and profile a noisy sales dataset', 'Create KPI dashboard visuals for management', 'Build and evaluate a simple prediction model', 'Write an executive summary from analysis results'],
                'course_type' => CourseTypeEnums::ADVANCED->value,
                'fees' => [40000, 46000, 51000],
            ],
            'Cyber-security' => [
                'course_titles' => ['Cybersecurity Essentials for Teams', 'Applied Security Operations and Incident Response', 'Defensive Security for Web and Cloud'],
                'lesson_titles' => ['Threat Modeling and Attack Surface', 'Authentication and Access Control', 'Vulnerability Assessment Workflow', 'Incident Response Playbook', 'Security Logging and Alerting'],
                'exercise_prompts' => ['Identify risks in a sample system architecture', 'Harden a login flow against common attacks', 'Draft incident timeline and containment plan', 'Create a monitoring checklist for suspicious behavior'],
                'course_type' => CourseTypeEnums::ADVANCED->value,
                'fees' => [48000, 53000, 58000],
            ],
            'Artificial Intelligence & Machine Learning' => [
                'course_titles' => ['Machine Learning for Product Teams', 'Applied AI with End-to-End Pipelines', 'Practical NLP and Recommendation Systems'],
                'lesson_titles' => ['Problem Framing for ML Projects', 'Data Preparation and Label Strategy', 'Model Training and Validation', 'Inference, Monitoring, and Drift', 'Responsible AI and Bias Mitigation'],
                'exercise_prompts' => ['Define ML success metrics for a business case', 'Train a baseline model and compare results', 'Set up a basic model monitoring plan', 'Document ethical risks and mitigations for deployment'],
                'course_type' => CourseTypeEnums::ADVANCED->value,
                'fees' => [50000, 56000, 62000],
            ],
            'Entrepreneurship' => [
                'course_titles' => ['Startup Launch Blueprint', 'Entrepreneurship from Idea to Revenue', 'Lean Business Validation Masterclass'],
                'lesson_titles' => ['Customer Discovery Interviews', 'Value Proposition and Positioning', 'MVP Scope and Prioritization', 'Go-to-Market Fundamentals', 'Unit Economics and Growth Metrics'],
                'exercise_prompts' => ['Create a one-page business model canvas', 'Write and test five customer interview questions', 'Define your MVP backlog with priorities', 'Build a 90-day launch execution plan'],
                'course_type' => CourseTypeEnums::BASIC->value,
                'fees' => [28000, 33000, 38000],
            ],
            'Marketing & Advertising' => [
                'course_titles' => ['Digital Marketing Strategy in Practice', 'Performance Marketing and Campaign Optimization', 'Brand Messaging and Conversion Copywriting'],
                'lesson_titles' => ['Audience Segmentation and Personas', 'Channel Mix and Budget Allocation', 'Campaign Setup and A/B Testing', 'Funnel Tracking and Attribution', 'Creative Performance Review'],
                'exercise_prompts' => ['Design a campaign plan for a product launch', 'Write ad copy variants for split testing', 'Analyze campaign metrics and optimize budget', 'Build a weekly reporting template for stakeholders'],
                'course_type' => CourseTypeEnums::BASIC->value,
                'fees' => [26000, 32000, 37000],
            ],
            'Graphic Design' => [
                'course_titles' => ['Graphic Design for Brand Systems', 'Visual Design Fundamentals to Portfolio', 'Design Thinking for Marketing Creatives'],
                'lesson_titles' => ['Typography and Hierarchy Basics', 'Color Systems and Accessibility', 'Layout Composition and Grids', 'Brand Identity Asset Creation', 'Client Feedback and Revision Cycles'],
                'exercise_prompts' => ['Create brand mood board and color palette', 'Design a social media ad set with variations', 'Produce a poster using grid-based layout', 'Document your design rationale and iteration notes'],
                'course_type' => CourseTypeEnums::BASIC->value,
                'fees' => [24000, 29000, 34000],
            ],
            'Digital Illustration' => [
                'course_titles' => ['Digital Illustration Techniques', 'Character and Environment Illustration Studio', 'Illustration Workflow for Commercial Projects'],
                'lesson_titles' => ['Sketching and Composition', 'Line Art and Shape Language', 'Lighting and Color Rendering', 'Texture and Detail Pass', 'Final Delivery for Client Use'],
                'exercise_prompts' => ['Sketch three composition options for one scene', 'Render a character with controlled lighting', 'Create a style-consistent illustration pack', 'Export assets in web and print-ready formats'],
                'course_type' => CourseTypeEnums::BASIC->value,
                'fees' => [25000, 30000, 35000],
            ],
            'Photography & Videography' => [
                'course_titles' => ['Photography and Video Production Basics', 'Content Creation for Social and Commercial Use', 'Visual Storytelling with Camera and Editing'],
                'lesson_titles' => ['Camera Settings and Exposure Control', 'Framing and Composition in Motion', 'Lighting Setup for Indoor and Outdoor', 'Editing Workflow and Color Balance', 'Publishing and Delivery Formats'],
                'exercise_prompts' => ['Shoot a 10-photo storytelling set', 'Record and edit a 60-second promo video', 'Apply color correction to raw footage', 'Create final export package for multiple platforms'],
                'course_type' => CourseTypeEnums::BASIC->value,
                'fees' => [27000, 32000, 37000],
            ],
            'Biology & Life Sciences' => [
                'course_titles' => ['Applied Biology for Modern Learners', 'Life Sciences Concepts and Lab Reasoning', 'Biology Foundations for Health Careers'],
                'lesson_titles' => ['Cell Structure and Function', 'Genetics and Inheritance', 'Human Body Systems Overview', 'Ecosystems and Biodiversity', 'Scientific Reporting Basics'],
                'exercise_prompts' => ['Label and explain cell organelle functions', 'Solve genetics inheritance practice cases', 'Compare two body systems using a concept map', 'Draft a short lab observation report'],
                'course_type' => CourseTypeEnums::BASIC->value,
                'fees' => [22000, 26000, 30000],
            ],
            'Chemistry' => [
                'course_titles' => ['Chemistry Fundamentals with Real Examples', 'Chemical Reactions and Problem Solving', 'Introductory Chemistry for STEM Pathways'],
                'lesson_titles' => ['Atomic Structure and Periodic Trends', 'Chemical Bonding and Molecular Shape', 'Stoichiometry and Reaction Balancing', 'Acids, Bases, and pH', 'Lab Safety and Measurement'],
                'exercise_prompts' => ['Balance a set of chemical equations', 'Calculate molar relationships for a reaction', 'Classify acids and bases from sample data', 'Prepare a basic lab safety checklist'],
                'course_type' => CourseTypeEnums::BASIC->value,
                'fees' => [23000, 27000, 31000],
            ],
            'Physics' => [
                'course_titles' => ['Physics in Everyday Systems', 'Core Physics for Engineering Students', 'Mechanics and Energy Problem Lab'],
                'lesson_titles' => ['Motion, Velocity, and Acceleration', 'Forces and Newtonian Mechanics', 'Work, Energy, and Power', 'Waves and Basic Electricity', 'Experimental Error and Data Analysis'],
                'exercise_prompts' => ['Solve kinematics word problems step-by-step', 'Analyze force diagrams for moving bodies', 'Compute energy transfer in simple systems', 'Summarize experiment findings with error notes'],
                'course_type' => CourseTypeEnums::BASIC->value,
                'fees' => [23000, 28000, 33000],
            ],
            'Medicine & Surgery' => [
                'course_titles' => ['Clinical Foundations for Medical Trainees', 'Essential Medicine and Surgical Principles', 'Patient-Centered Care in Clinical Practice'],
                'lesson_titles' => ['Clinical Assessment Basics', 'Anatomy Review for Procedures', 'Common Acute Condition Triage', 'Pre-Operative and Post-Operative Care', 'Medical Documentation Standards'],
                'exercise_prompts' => ['Create a triage checklist for emergency intake', 'Document a sample patient SOAP note', 'Map pre-op to post-op care steps', 'Review and improve a clinical communication scenario'],
                'course_type' => CourseTypeEnums::ADVANCED->value,
                'fees' => [52000, 58000, 64000],
            ],
            'Nursing & Healthcare' => [
                'course_titles' => ['Nursing Practice and Patient Safety', 'Healthcare Workflow and Care Coordination', 'Professional Nursing Skills for Modern Clinics'],
                'lesson_titles' => ['Patient Vitals and Monitoring', 'Medication Administration Safety', 'Infection Prevention Protocols', 'Care Plan Documentation', 'Communication in Multidisciplinary Teams'],
                'exercise_prompts' => ['Prepare a patient monitoring sheet', 'Review medication safety checkpoints', 'Design an infection control mini-protocol', 'Write a handoff note for shift transition'],
                'course_type' => CourseTypeEnums::ADVANCED->value,
                'fees' => [46000, 51000, 56000],
            ],
        ];

        if (isset($profiles[$subCategoryName])) {
            return $profiles[$subCategoryName];
        }

        return [
            'course_titles' => [
                $subCategoryName.' Professional Pathway',
                $subCategoryName.' Practical Skills Program',
                $subCategoryName.' Applied Foundations',
            ],
            'lesson_titles' => [
                'Core Concepts and Terminology',
                'Tools, Methods, and Workflows',
                'Hands-on Practice Session',
                'Quality, Review, and Improvement',
                'Final Project Planning',
            ],
            'exercise_prompts' => [
                'Complete a practical case-based task using learned concepts',
                'Document your workflow and decisions for peer review',
                'Review outcomes and propose improvements',
                'Prepare a mini project deliverable for assessment',
            ],
            'course_type' => CourseTypeEnums::BASIC->value,
            'fees' => [25000, 30000, 35000],
        ];
    }

    /**
     * @param  array<int, string>  $items
     * @return array<int, string>
     */
    private function rotateAndTake(array $items, int $shift, int $take): array
    {
        if (empty($items)) {
            return [];
        }

        $count = count($items);
        $result = [];
        for ($i = 0; $i < $take; $i++) {
            $result[] = $items[($shift + $i) % $count];
        }

        return $result;
    }

    private function courseDescription(string $subcategoryName, string $courseTitle): string
    {
        return '<p><strong>'.$courseTitle.'</strong> is designed for real-world '.$subcategoryName.' outcomes.</p>'
            .'<p>You will work through practical workflows, case-based learning, and delivery standards used in industry teams.</p>'
            .'<p>By the end of this course, learners can execute tasks independently and communicate results clearly.</p>';
    }

    private function lessonBody(string $subcategoryName, string $courseTitle, string $lessonTitle, int $lessonOrder): string
    {
        return '<h3>Lesson '.$lessonOrder.': '.$lessonTitle.'</h3>'
            .'<p>This lesson is part of <strong>'.$courseTitle.'</strong> under <strong>'.$subcategoryName.'</strong>.</p>'
            .'<ul>'
            .'<li>Understand the objective and required workflow.</li>'
            .'<li>Apply concepts through a short practical task.</li>'
            .'<li>Review quality checkpoints and common mistakes.</li>'
            .'</ul>';
    }

    private function isGenericCourseTitle(string $title): bool
    {
        return (bool) preg_match('/^course(?:\s+\d+)?$/i', trim($title));
    }

    private function isGenericLessonTitle(string $title): bool
    {
        return (bool) preg_match('/^lesson(?:\s+\d+)?$/i', trim($title));
    }

    private function isGenericExerciseTitle(string $title): bool
    {
        return (bool) preg_match('/^exercise(?:\s+\d+)?$/i', trim($title));
    }
}
