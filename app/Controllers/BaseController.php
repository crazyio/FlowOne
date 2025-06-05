<?php

namespace App\Controllers;

/**
 * Base Controller
 *
 * This is a basic placeholder for BaseController.
 * It can be extended with common functionalities for other controllers.
 */
class BaseController
{
    public function __construct()
    {
        // Initialize common properties or call methods here
        // For example, loading a session helper or a database connection
    }

    /**
     * Example of a helper method that could be used by child controllers.
     *
     * @param string $viewName The name of the view file (e.g., 'dashboard/index')
     * @param array $data Data to pass to the view
     * @param array $options Options for rendering (e.g., layout)
     * @return void
     */
    protected function render(string $viewName, array $data = [], array $options = []): void
    {
        // This is a very basic render example.
        // A real application would have a more sophisticated view rendering system.
        // It might integrate with the view() function or a templating engine.

        // Extract data for easier access in the view
        extract($data);

        // Determine layout
        $layoutFile = VIEWS_PATH . '/layouts/' . ($options['layout'] ?? 'app') . '.php';
        $viewFile = VIEWS_PATH . '/' . str_replace('.', '/', $viewName) . '.php';

        if (isset($options['layout']) && $options['layout'] === false) {
            // No layout, just render the view
            if (file_exists($viewFile)) {
                require $viewFile;
            } else {
                echo "Error: View file '{$viewFile}' not found.";
            }
        } else {
            // Render with layout
            if (file_exists($layoutFile)) {
                // The view content will be captured and passed to the layout
                ob_start();
                if (file_exists($viewFile)) {
                    require $viewFile;
                } else {
                    echo "Error: View file '{$viewFile}' not found.";
                }
                $content = ob_get_clean(); // Get the content of the view
                require $layoutFile; // Render the layout, which should use $content
            } else {
                echo "Error: Layout file '{$layoutFile}' not found.";
            }
        }
    }

    // Placeholder for a global view() function if it's part of the framework design
    // If your framework provides a global view() function, you might not need this in BaseController,
    // or DashboardController might call it directly.
    // For now, assuming controllers might need to call a render method like this one.
}

// It's also common for frameworks to provide a global helper function for views.
// If your application uses a global view() function like in DashboardController:
// function view(string $viewName, array $data = [], array $options = []) {
//     // This global function would need access to VIEWS_PATH or similar constants
//     // and would encapsulate the logic found in the render() method above.
//     // For now, we'll assume controllers will use $this->render() or similar if BaseController provides it.
//     // Or, the view() function is defined elsewhere (e.g. a helpers file).
//     // The current DashboardController.php uses a global `view()` function.
//     // Let's assume this function is defined elsewhere for now.
// }
