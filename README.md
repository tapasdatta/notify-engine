# Rule-Based Notification Engine

## Overview

The Rule-Based Notification Engine is a powerful, customizable notification system that allows users to create and manage complex rules for sending notifications. The system supports a wide range of use cases, from inactivity alerts (e.g., sending an email if a user hasn’t logged in for a specified period) to high-priority alerts for critical actions (e.g., triggering an SMS notification for high-value transactions). Built to be flexible and extensible, this engine efficiently handles high-throughput data and ensures timely notifications through email or SMS.

## Features

- **Custom Rule Setup**: Users can define rules to receive notifications based on specific conditions.
  - **Inactivity Rule**: Send notifications if a user has been inactive for a set number of days.
  - **Transaction Threshold**: Trigger alerts if transactions exceed a specified amount.
  - **Transaction Count**: Notify if the daily transaction count surpasses a limit.
- **Multi-Channel Notifications**: Supports both **email** and **SMS** alerts based on user preferences.
- **Asynchronous Processing**: Pub/Sub architecture with RabbitMQ ensures high-performance handling of real-time notifications.
- **Flexible Rule Engine**: Dynamically evaluates rules for each user, making it easy to add or modify rules as needed.

## Technologies Used

- **Backend Framework**: Laravel
- **Database**: MongoDB
- **Messaging Queue**: RabbitMQ (Pub/Sub)
- **Notifications**: 
  - **SMS**: Nexmo
  - **Email**: Resend
- **Other Technologies**: Queue management for handling high-volume transactions, RESTful API design, and a robust rule evaluation engine.

## Project Structure

- **Controllers**: Manage API endpoints for setting up and triggering notifications.
- **Services**: Encapsulate rule evaluation logic, making it modular and maintainable.
- **Database**: Optimized to store large volumes of user activity and rules, with efficient querying for rule conditions.
- **Messaging Queue**: Used to process notifications asynchronously, ensuring fast and reliable communication.

## Getting Started

1. **Clone the repository**:
   ```bash
   git clone https://github.com/tapasdatta/notify-engine.git

2. **Install dependencies:**:
    ```bash
    composer install

3. **Set up environment variables (e.g., MongoDB, RabbitMQ, Nexmo, and Resend credentials).**:

4. **Run the application:**:
    ```bash
    php artisan serve

5. **Queue workers: Make sure to start workers for handling asynchronous notifications:**:
    ```bash
    php artisan queue:work
    
    

This README provides a clear and professional overview of your **Rule-Based Notification Engine** and is structured to highlight its purpose, features, and technologies used.
