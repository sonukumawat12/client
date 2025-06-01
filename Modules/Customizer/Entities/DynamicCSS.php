<?php 

namespace Modules\Customizer\Entities;

use Illuminate\Support\Facades\Config;

class DynamicCSS
{

    public static function generateFrontEndCSS()
    {
        // header
        $css = "
            .section-heading {
                background-color: ".setting('frontend_hero_bg_color', '#3f3d56db')." !important;
                background-image: url(".setting('frontend_hero_bg_image', '/images/pattern.svg').");
            }

            .btn-submit-ticket {
                color: ".setting('frontend_ticket_btn_text_color', '#fff')." !important;
                border-color: ".setting('frontend_ticket_btn_border_color', 'red')."  !important;
                background-color: ".setting('frontend_ticket_btn_bg_color', '#fffff00')."  !important;
            }
            
            .btn-submit-ticket:hover {
                color: ".setting('frontend_ticket_btn_hover_text_color', '#fff')." !important;
                border-color: ".setting('frontend_ticket_btn_hover_border_color', 'red')." !important;
                background-color: ".setting('frontend_ticket_btn_hover_bg_color', 'red')." !important;
            }

            .submit-ticket {
                background-color: ".setting('frontend_main_bg_color', 'red')."  !important;
            }

            .bg-login-image {
                background-image: url(".setting('frontend_auth_bg_image', '/images/auth.svg').");
            }

        ";

        $optimizedCSS = self::optimizeCSS($css);

        $styleFile = public_path('build/frontend/css/style.css');

        if ( ! file_exists($styleFile) ) {
          touch($styleFile);
        }

        file_put_contents($styleFile, $optimizedCSS);

        $vars = Config::get('vars');

        $vars['asset_version'] = uniqid();

        $varsFileString = "<?php\n\n";
        $varsFileString .= "return " . var_export($vars, true) . ";";

        file_put_contents(config_path('vars.php'), $varsFileString);
    }

    public static function optimizeCSS($css) {

        // remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);

        // remove tabs, spaces, new lines, etc.       
        $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);

        // remove unnecessary spaces
        $css = str_replace('{ ', '{', $css);
        $css = str_replace(' }', '}', $css);
        $css = str_replace('; ', ';', $css);
        $css = str_replace(', ', ',', $css);
        $css = str_replace(' {', '{', $css);
        $css = str_replace('} ', '}', $css);
        $css = str_replace(': ', ':', $css);
        $css = str_replace(' ,', ',', $css);
        $css = str_replace(' ;', ';', $css);
            
        return $css;
    }

}