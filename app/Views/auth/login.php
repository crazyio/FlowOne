<div class="login-box">
    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger" style="color: red; background-color: #ffebee; border: 1px solid #ef9a9a; padding: 10px; margin-bottom: 15px; border-radius: 6px;">
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success" style="color: green; background-color: #e8f5e9; border: 1px solid #a5d6a7; padding: 10px; margin-bottom: 15px; border-radius: 6px;">
            <?php echo htmlspecialchars($successMessage); ?>
        </div>
    <?php endif; ?>
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
