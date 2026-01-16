# Postman Collections for Auth Module

This directory contains Postman collections and environments for testing the E-Commerce Auth Module API endpoints.

## Files

### Collections
- **`auth-complete-collection.json`** - Complete collection with all authentication and password reset endpoints including tests

### Environments
- **`auth-environment.json`** - Development environment configuration

## Quick Start

### 1. Import into Postman

1. Open Postman
2. Click **Import** button (top left)
3. Drag and drop both files or click **Upload Files**
4. Select both `auth-complete-collection.json` and `auth-environment.json`
5. Click **Import**

### 2. Select Environment

1. In the top right corner of Postman, click the environment dropdown
2. Select **"E-Commerce Auth - Development"**

### 3. Configure Base URL

The default base URL is `http://localhost:8003`. To change it:

1. Click the environment quick look (eye icon) in the top right
2. Click **Edit** next to the environment name
3. Update the `base_url` value
4. Click **Save**

## Collection Structure

### 📁 Authentication
Core authentication endpoints:
- **Register** - Create new user account
- **Login** - Login with email/password
- **Basic Auth Login** - HTTP Basic authentication
- **Get User Profile (Me)** - Get authenticated user info
- **Refresh Token** - Refresh access token
- **Logout** - Invalidate current token

### 📁 Password Reset
Password reset flow:
- **Forgot Password** - Request password reset token
- **Reset Password** - Reset password with token

### 📁 Password Management
Password management for authenticated users:
- **Update Password** - Change password (requires authentication)

### 📁 Error Cases
Test validation and error handling:
- Login with invalid credentials
- Register with existing email
- Access protected endpoint without token
- Forgot password with non-existent email
- Reset password with invalid token
- Update password with wrong current password
- Reset password with mismatched confirmation

### 📁 Complete Workflow Tests
End-to-end workflow scenarios:
1. Register New User
2. Get User Profile
3. Update Password
4. Login with New Password
5. Logout

## Environment Variables

The environment includes the following variables:

| Variable | Description | Default Value |
|----------|-------------|---------------|
| `base_url` | API base URL | `http://localhost:8003` |
| `token` | Access token (auto-saved) | _(empty)_ |
| `reset_token` | Password reset token (auto-saved) | _(empty)_ |
| `test_email` | Test email for requests | `john@test.com` |

## Auto-Saved Variables

The collection automatically saves tokens for you:

- **Login/Register** → Saves `token` for authenticated requests
- **Refresh Token** → Updates `token` with new token
- **Forgot Password** → Saves `reset_token` (development only)
- **Update Password** → Updates `token` after password change

## Usage Examples

### Basic Authentication Flow

1. **Register a new user**
   - Run "Register" request
   - Token is automatically saved

2. **Access protected endpoint**
   - Run "Get User Profile (Me)"
   - Uses saved token automatically

3. **Logout**
   - Run "Logout" request
   - Token is invalidated

### Password Reset Flow

1. **Request password reset**
   - Run "Forgot Password" request
   - Reset token is automatically saved (development mode)

2. **Reset password**
   - Run "Reset Password" request
   - Uses saved reset token and email

3. **Login with new password**
   - Update the password in "Login" request
   - Run "Login" request

### Password Update Flow (Authenticated)

1. **Login first**
   - Run "Login" request
   - Token is automatically saved

2. **Update password**
   - Run "Update Password" request
   - New token is automatically saved

3. **Verify new password works**
   - Run "Login" request with new password

## Test Scripts

All requests include test scripts that automatically verify:
- ✅ Correct HTTP status codes
- ✅ Response structure validation
- ✅ Token auto-saving
- ✅ Error message validation

View test results in the **Test Results** tab after running a request.

## Running Tests

### Individual Request
1. Select a request
2. Click **Send**
3. View results in **Test Results** tab

### Entire Folder
1. Right-click on a folder (e.g., "Authentication")
2. Click **Run folder**
3. View results in Collection Runner

### Entire Collection
1. Click the three dots (**...**) next to the collection name
2. Click **Run collection**
3. View results in Collection Runner

## Customizing Requests

### Change Test Email
1. Click environment dropdown → Edit environment
2. Update `test_email` variable
3. Save changes

### Change Base URL
1. Click environment dropdown → Edit environment
2. Update `base_url` variable
3. Save changes

### Add Custom Headers
1. Select a request
2. Go to **Headers** tab
3. Add/modify headers as needed

## Tips

### Use Variables in Request Body
You can use environment variables in any request:
```json
{
    "email": "{{test_email}}",
    "password": "password123"
}
```

### Copy Token Manually
If you need the token value:
1. Click environment quick look (eye icon)
2. Find `token` variable
3. Click to copy value

### Test Different Scenarios
- Create multiple environments (Development, Staging, Production)
- Duplicate requests to test different data
- Use Pre-request Scripts for dynamic data generation

## Troubleshooting

### Token Not Saving
- Check that the environment is selected (top right)
- Verify the request completed successfully (200/201 status)
- Check Console (View → Show Postman Console) for errors

### Connection Refused
- Ensure Laravel server is running (`php artisan serve`)
- Verify the base URL matches your server address
- Check firewall settings

### 401 Unauthorized
- Ensure you're logged in first
- Check that token is saved in environment
- Token may have expired - try logging in again

### 422 Validation Error
- Check request body format
- Verify required fields are present
- Review error messages in response body

## Additional Resources

- [Password Reset Guide](../PASSWORD_RESET_GUIDE.md) - Detailed API documentation
- [Auth Module README](../README.md) - Module overview
- [Postman Documentation](https://learning.postman.com/docs/getting-started/introduction/) - Official Postman docs

## Support

For issues or questions about the API:
1. Check the Laravel logs: `storage/logs/laravel.log`
2. Review validation errors in response body
3. Ensure database migrations are run
4. Verify `.env` configuration

## Version

- Collection Version: 2.1.0
- Last Updated: January 15, 2026
- Compatible with: Laravel 11.x, Sanctum 4.x
