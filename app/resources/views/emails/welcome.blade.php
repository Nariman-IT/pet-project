<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добро пожаловать</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 30px; border-radius: 5px;">
        <h1 style="color: #007bff; margin-top: 0;">Добро пожаловать, {{ $user['name'] }}!</h1>
        
        <p>Спасибо за регистрацию в нашем сервисе!</p>
        
        <div style="background-color: white; padding: 20px; border-radius: 5px; margin: 20px 0;">
            <p><strong>Ваши данные:</strong></p>
            <p>Email: {{ $user['email'] }}</p>
            <p>Дата регистрации: {{ now()->format('d.m.Y H:i') }}</p>
        </div>
        
        <p>Для начала работы подтвердите ваш email, перейдя по ссылке:</p>
        
        <a href="{{ url('/verify-email') }}" 
           style="display: inline-block; background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px; margin: 10px 0;">
            Подтвердить Email
        </a>
        
        <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">
        
        <p style="color: #666; font-size: 12px;">
            © {{ date('Y') }} {{ config('app.name') }}. Все права защищены.<br>
            Вы получили это письмо, потому что зарегистрировались на нашем сайте.
        </p>
    </div>
</body>
</html>