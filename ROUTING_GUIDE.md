# GameCritic Routing Guide

## Correct URL Structure

Since your project is in `D:\xampp\htdocs\Gamecritic`, you need to access it with the full path:

### Base URL:
```
http://localhost:881/Gamecritic/public/
```

### Common Routes:
- **Homepage**: `http://localhost:881/Gamecritic/public/`
- **Login**: `http://localhost:881/Gamecritic/public/login`
- **Dashboard**: `http://localhost:881/Gamecritic/public/dashboard`
- **Followers**: `http://localhost:881/Gamecritic/public/followers`
- **Chat**: `http://localhost:881/Gamecritic/public/chat`
- **Notifications**: `http://localhost:881/Gamecritic/public/notifications`
- **Game Page**: `http://localhost:881/Gamecritic/public/game/1`

## Troubleshooting 404 Errors

If you're getting a 404 error:

1. **Check the URL**: Make sure it includes `/Gamecritic/public/` in the path
2. **Check Apache**: Make sure mod_rewrite is enabled
3. **Check .htaccess**: The file should be in the `public/` folder
4. **Check Port**: Your server is running on port 881 (not the default 80)

## Quick Test

Try accessing: `http://localhost:881/Gamecritic/public/`

If that works, then the routing is working correctly. If you still get 404, check:
- Apache error logs
- PHP error logs
- Make sure the `public/.htaccess` file exists


