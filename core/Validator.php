<?php
// core/Validator.php - Iranian Specific Validation

class Validator {
    
    // Validate Iranian Mobile Number (09xx, 11 digits)
    public static function validateMobile(string $mobile): bool {
        return preg_match('/^09[0-9]{9}$/', $mobile) === 1;
    }

    // Validate Iranian National ID (کد ملی) - 10 digits with checksum
    public static function validateNationalId(string $nationalId): bool {
        if (!preg_match('/^[0-9]{10}$/', $nationalId)) {
            return false;
        }

        // Check for all same digits (invalid)
        if (preg_match('/^(\d)\1{9}$/', $nationalId)) {
            return false;
        }

        $digits = str_split($nationalId);
        $sum = 0;
        
        for ($i = 0; $i < 9; $i++) {
            $sum += $digits[$i] * (10 - $i);
        }

        $remainder = $sum % 11;
        $controlDigit = $digits[9];

        if ($remainder < 2) {
            return $controlDigit == $remainder;
        } else {
            return $controlDigit == (11 - $remainder);
        }
    }

    // Validate Economic Code (کد اقتصادی) - Usually 11 or 12 digits
    public static function validateEconomicCode(string $code): bool {
        return preg_match('/^[0-9]{11,12}$/', $code) === 1;
    }

    // Validate Sheba (IBAN Iran) - IR + 24 digits
    public static function validateSheba(string $sheba): bool {
        $sheba = strtoupper(str_replace(' ', '', $sheba));
        return preg_match('/^IR\d{24}$/', $sheba) === 1;
    }

    // Validate Postal Code (10 digits)
    public static function validatePostalCode(string $postalCode): bool {
        return preg_match('/^[0-9]{10}$/', $postalCode) === 1;
    }

    // Sanitize input data
    public static function sanitize(string $data): string {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    // Convert Persian digits to English for validation
    public static function toEnglishDigits(string $str): string {
        $persianNumbers = [/۰/g, /۱/g, /۲/g, /۳/g, /۴/g, /۵/g, /۶/g, /۷/g, /۸/g, /۹/g];
        $arabicNumbers  = [/٠/g, /١/g, /٢/g, /٣/g, /٤/g, /٥/g, /٦/g, /٧/g, /٨/g, /٩/g];
        
        for($i = 0; $i < 10; $i++) {
            $str = preg_replace($persianNumbers[$i], $i, $str);
            $str = preg_replace($arabicNumbers[$i], $i, $str);
        }
        return $str;
    }

    // Normalize Persian/Arabic characters for search
    public static function normalizePersian(string $text): string {
        $search = ['ي', 'ك', 'ـ', '‌', 'ۀ'];
        $replace = ['ی', 'ک', '', '', 'ه'];
        return str_replace($search, $replace, $text);
    }
}