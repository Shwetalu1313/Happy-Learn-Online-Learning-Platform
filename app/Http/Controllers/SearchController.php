<?php

namespace App\Http\Controllers;

use App\Enums\CourseStateEnums;
use App\Enums\UserRoleEnums;
use App\Models\Category;
use App\Models\Course;
use App\Models\JobPost;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request): \Illuminate\View\View
    {
        $query = trim((string) $request->query('q', ''));
        $isAdmin = auth()->user()?->role?->value === UserRoleEnums::ADMIN->value;

        $results = [
            'courses' => collect(),
            'categories' => collect(),
            'subCategories' => collect(),
            'jobs' => collect(),
            'users' => collect(),
        ];

        if (mb_strlen($query) >= 2) {
            $results['courses'] = Course::query()
                ->select(['id', 'title', 'description', 'state', 'courseType', 'fees', 'sub_category_id', 'created_at'])
                ->with(['sub_category:id,name,category_id', 'sub_category.category:id,name'])
                ->when(! $isAdmin, function (Builder $builder): void {
                    $builder->where('state', CourseStateEnums::APPROVED->value)
                        ->has('lessons');
                })
                ->where(function (Builder $builder) use ($query): void {
                    $this->applyLikeSearch($builder, ['title', 'description'], $query);
                })
                ->orderByDesc('created_at')
                ->limit(15)
                ->get();

            $results['categories'] = Category::query()
                ->select(['id', 'name', 'created_at'])
                ->where('name', 'like', '%'.$query.'%')
                ->orderBy('name')
                ->limit(10)
                ->get();

            $results['subCategories'] = SubCategory::query()
                ->select(['id', 'name', 'category_id', 'created_at'])
                ->with('category:id,name')
                ->where('name', 'like', '%'.$query.'%')
                ->orderBy('name')
                ->limit(10)
                ->get();

            $results['jobs'] = JobPost::query()
                ->select(['id', 'title', 'requirements', 'created_at'])
                ->where(function (Builder $builder) use ($query): void {
                    $this->applyLikeSearch($builder, ['title', 'requirements'], $query);
                })
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

            if ($isAdmin) {
                $results['users'] = User::query()
                    ->select(['id', 'name', 'email', 'role', 'created_at'])
                    ->where(function (Builder $builder) use ($query): void {
                        $this->applyLikeSearch($builder, ['name', 'email'], $query);
                    })
                    ->orderBy('name')
                    ->limit(10)
                    ->get();
            }
        }

        $total = collect($results)->sum(fn ($group) => $group->count());

        return view('search.global', [
            'titlePage' => 'Global Search',
            'breadcrumbs' => [
                ['link' => $isAdmin ? route('dashboard') : route('home'), 'name' => $isAdmin ? 'Dashboard' : 'Home'],
                ['name' => 'Global Search', 'active' => true],
            ],
            'query' => $query,
            'results' => $results,
            'resultTotal' => $total,
            'isAdminSearchView' => $isAdmin,
            'layout' => $isAdmin ? 'admin.layouts.app' : 'layouts.app',
        ]);
    }

    private function applyLikeSearch(Builder $builder, array $columns, string $query): void
    {
        $builder->where(function (Builder $nested) use ($columns, $query): void {
            foreach ($columns as $index => $column) {
                if ($index === 0) {
                    $nested->where($column, 'like', '%'.$query.'%');
                } else {
                    $nested->orWhere($column, 'like', '%'.$query.'%');
                }
            }
        });
    }
}
