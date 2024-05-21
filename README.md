
# Online Learning and Job Opportunities Platform

<img src="https://1drv.ms/i/c/c0a2a77198fbdcb5/EafOE4YDtJ1Mp5-eNTMJFcsBO1giARW0jmDha-BcKvM4YA?e=TXT9Y0">

![System Architecture](https://1drv.ms/i/c/c0a2a77198fbdcb5/EafOE4YDtJ1Mp5-eNTMJFcsBO1giARW0jmDha-BcKvM4YA?e=TXT9Y0)
![User Interface](https://1drv.ms/i/c/c0a2a77198fbdcb5/Eb_kudyH3pNFtlZbjXefBDgBiEbD-dY4HJbRdylSw9Ye-w?e=H0XlAu)
![Job Opportunities Module](https://1drv.ms/i/c/c0a2a77198fbdcb5/EWhEFAXUdBpMmhZ2hH1Gco8BDGFQoXV4dz3kV3-kp_Q56A?e=s1wxBO)
![Language Services Module](https://1drv.ms/i/c/c0a2a77198fbdcb5/EXR7vDLTcYlEhNNrapERN-sB1JKMAaOXWjf9c77bJq0I3g?e=Yd7Gzz)

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
