<h1 align="center">💬 Real-Time Chatting Application</h1>

A Laravel + Vue.js based real-time one-to-one chat application using Echo and Pusher.

---

## 🚀 Features

- Realtime private messaging
- Laravel backend with broadcasting
- Vue.js frontend
- Authentication with registration and login
- Queue worker support

---

## ⚙️ How to Set Up Locally

### Step 1: Clone the repository

```bash
git clone https://github.com/developeralamin/chat
cd chat
```

- Step:2 Install dependencies 
```bash
   composer install
   npm install 
```

- Step:3 Create a copy of your .env file
```
cp .env.example  .env
```

- Step:4 Generate application key
```bash
php artisan key:generate
```

- Step:5  Create Database Name .env file 

```bash
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
```
- Step:6 Run migrations
```bash
 php artisan migrate
```

- Step:7 Run the application In a separate terminal:
```bash
php artisan serve 
 
npm run dev
```
Step 8: Start the queue  and default queue connection is database (for broadcasting)
```bash
php artisan queue:work
```

Step 8: Default queue connection is database (for broadcasting)
```bash
  QUEUE_CONNECTION=database
```
-
Access the Application

Visit http://localhost:8000 in your browser.
 - Create a new account
 - Log in using your email and password
 - Start chatting in real time!





![Screenshot_174](https://github.com/user-attachments/assets/e01e1338-e19f-43f3-8c83-da455d34f972)
![Screenshot_176](https://github.com/user-attachments/assets/18b86128-0986-45f0-943a-24fca37d2130)
![Screenshot_175](https://github.com/user-attachments/assets/74701019-664a-4c79-b951-0f962646fd1d)


