
# Online Learning and Job Opportunities Platform

<img src="https://github.com/Shwetalu1313/Happy-Learn-Online-Learning-Platform/blob/main/storage/app/public/Shared%20Photos/admin-dashboard.png" alt="Admin Dashboard" width="600"/>

<img src="https://github.com/Shwetalu1313/Happy-Learn-Online-Learning-Platform/blob/main/storage/app/public/Shared%20Photos/final-year-home.png" alt="User Home Page" width="600"/>

<img src="https://github.com/Shwetalu1313/Happy-Learn-Online-Learning-Platform/blob/main/storage/app/public/Shared%20Photos/Galaxy-Fold-user-profile.png" alt="User Profile (Galaxy Fold)" width="600"/>

<img src="https://github.com/Shwetalu1313/Happy-Learn-Online-Learning-Platform/blob/main/storage/app/public/Shared%20Photos/iPhone-13-PRO-MAX-home.png" alt="User Home Page (iPhone 13 Pro)" width="600"/>

## Introduction

Welcome to the Online Learning and Job Opportunities Platform! This project is developed as a final year graduate project using the DSDM Agile methodology. It provides an online platform for learning, job opportunities, and language services. The system is developed solo, adhering to the ethics and laws of the BCS (British Computer Society).

## System Scope

| ID   | Functional Requirement                                                 |
|------|------------------------------------------------------------------------|
| FR1  | User registration and authentication                                   |
| FR2  | Course creation and management                                         |
| FR3  | Enrollment in courses                                                  |
| FR4  | Job listings and applications                                          |
| FR5  | Language translation services                                          |
| FR6  | User profile management                                                |
| FR7  | Course and Question and Answers progress tracking                      |
| FR8  | Messaging system between users and instructors                         |
| FR9  | Review and rating system for courses and jobs                          |
| FR10 | Administrative dashboard for managing users, courses, and job listings |
| FR11 | Course Contributor Access Sharing |                                     |

## Installation Instructions

To set up the project locally, follow these steps:

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/your-repository.git
   cd your-repository

2. **Install composer dependencies**
   ```bash
   composer install
   
3. **Copy the example environment file and configure it**
   ```bash
   cp .env.example .env
   php artisan key:generate
   
4. **Configure the `.env` file**
   ```bash
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   
5. **Run Database Migrations**
   ```bash
   php artisan migrate
   
6. **Seed the database (optional)**
   ```bash
   php artisan serve

## Ethics and Laws
This project is developed in adherence to the ethical standards and laws of the British Computer Society (BCS). The platform ensures user privacy, data security, and fair use policies.

## Conclusion
Thank you for checking out the Online Learning and Job Opportunities Platform. For any issues or contributions, feel free to open an issue or submit a pull request on the GitHub repository.
