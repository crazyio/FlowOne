<div class="login-box">
    <h2>Login to Flow One</h2>
    <form action="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/login" method="POST"> <!-- Action URL to be handled by router -->
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <input type="checkbox" id="remember" name="remember">
            <label for="remember">Remember Me</label>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    <p><a href="#">Forgot Password?</a></p>
</div>
