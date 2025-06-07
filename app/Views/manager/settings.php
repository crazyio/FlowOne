<?php
use App\Core\Language;
Language::init();
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-1"><?php echo Language::get('settings.title'); ?></h2>
                    <p class="card-text text-muted mb-0"><?php echo Language::get('settings.personal_info'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages -->
    <?php if (!empty($successMessage)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($successMessage); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($errorMessage); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Settings Form -->
    <div class="row">
        <div class="col-lg-8 col-md-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><?php echo Language::get('settings.personal_info'); ?></h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/manager/settings" method="POST">
                        <div class="row">
                            <!-- Name -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label"><?php echo Language::get('settings.name'); ?> <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       value="<?php echo htmlspecialchars($userData['name'] ?? ''); ?>" 
                                       required>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label"><?php echo Language::get('settings.email'); ?> <span class="text-danger">*</span></label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" 
                                       required>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Phone -->
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label"><?php echo Language::get('settings.phone'); ?></label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="phone" 
                                       name="phone" 
                                       value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>">
                            </div>

                            <!-- Language -->
                            <div class="col-md-6 mb-3">
                                <label for="language" class="form-label"><?php echo Language::get('settings.language'); ?></label>
                                <select class="form-select" id="language" name="language">
                                    <?php foreach ($supportedLanguages as $code => $langData): ?>
                                        <option value="<?php echo htmlspecialchars($code); ?>" 
                                                <?php echo ($currentLanguage === $code) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($langData['native_name']); ?>
                                            (<?php echo htmlspecialchars($langData['name']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">
                                    <?php if (Language::isRTL()): ?>
                                        שפה זו תשפיע על כיוון הטקסט ועל התרגומים באפליקציה
                                    <?php else: ?>
                                        This will affect the text direction and translations in the application
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Language Preview -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <?php if (Language::isRTL()): ?>
                                                תצוגה מקדימה של השפה
                                            <?php else: ?>
                                                Language Preview
                                            <?php endif; ?>
                                        </h6>
                                        <div class="<?php echo Language::getDirectionClass(); ?>">
                                            <p class="mb-1">
                                                <strong><?php echo Language::get('dashboard.welcome', ['name' => $userData['name'] ?? 'User']); ?></strong>
                                            </p>
                                            <p class="mb-1">
                                                <?php echo Language::get('nav.dashboard'); ?> | 
                                                <?php echo Language::get('nav.clients'); ?> | 
                                                <?php echo Language::get('nav.tasks'); ?>
                                            </p>
                                            <small class="text-muted">
                                                <?php echo Language::get('dashboard.overview'); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/dashboard" 
                                       class="btn btn-secondary">
                                        <?php echo Language::get('common.cancel'); ?>
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        <?php echo Language::get('settings.save'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="col-lg-4 col-md-2">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <?php if (Language::isRTL()): ?>
                            עזרה ומידע
                        <?php else: ?>
                            Help & Information
                        <?php endif; ?>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="<?php echo Language::getDirectionClass(); ?>">
                        <?php if (Language::isRTL()): ?>
                            <h6>שפות נתמכות:</h6>
                            <ul class="list-unstyled">
                                <li><strong>עברית</strong> - טקסט מימין לשמאל</li>
                                <li><strong>English</strong> - Left to right text</li>
                            </ul>
                            
                            <h6 class="mt-3">הערות חשובות:</h6>
                            <ul class="small">
                                <li>שינוי השפה ישפיע על כל הממשק</li>
                                <li>העדפת השפה נשמרת עבור הכניסות הבאות</li>
                                <li>טקסט בעברית מוצג מימין לשמאל</li>
                            </ul>
                        <?php else: ?>
                            <h6>Supported Languages:</h6>
                            <ul class="list-unstyled">
                                <li><strong>English</strong> - Left to right text</li>
                                <li><strong>עברית</strong> - Right to left text</li>
                            </ul>
                            
                            <h6 class="mt-3">Important Notes:</h6>
                            <ul class="small">
                                <li>Language changes affect the entire interface</li>
                                <li>Language preference is saved for future logins</li>
                                <li>Hebrew text is displayed right-to-left</li>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
    // Preview language changes
    document.getElementById('language').addEventListener('change', function() {
        // You could add AJAX here to preview translations without page reload
        // For now, we'll just show a simple message
        const selectedLang = this.value;
        const isRTL = selectedLang === 'he';
    
        // Update preview direction
        const preview = document.querySelector('.card.bg-light .card-body > div');
        if (preview) {
            preview.className = isRTL ? 'rtl' : 'ltr';
        }
    });
</script>