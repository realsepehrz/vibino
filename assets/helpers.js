// assets/helpers.js

const PersianUtils = {
    toPersianDigits(n) {
        const farsiDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return n.toString().replace(/\d/g, x => farsiDigits[x]);
    },

    toEnglishDigits(str) {
        const persianNumbers = [/۰/g, /۱/g, /۲/g, /۳/g, /۴/g, /۵/g, /۶/g, /۷/g, /۸/g, /۹/g];
        const arabicNumbers  = [/٠/g, /١/g, /٢/g, /٣/g, /٤/g, /٥/g, /٦/g, /٧/g, /٨/g, /٩/g];
        
        if(typeof str === 'number') return str;
        
        for(let i=0; i<10; i++) {
            str = str.replace(persianNumbers[i], i).replace(arabicNumbers[i], i);
        }
        return str;
    },

    formatCurrency(amount, type = 'rial') {
        let num = parseInt(this.toEnglishDigits(amount.toString()));
        if (isNaN(num)) return '0';

        if (type === 'toman') {
            num = Math.round(num / 10);
        }

        return this.toPersianDigits(num.toLocaleString('en-US')) + ' ' + (type === 'toman' ? 'تومان' : 'ریال');
    },

    gregorianToJalali(gy, gm, gd) {
        const g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        let jy = (gy <= 1600) ? 0 : 979;
        gy -= (gy <= 1600) ? 621 : 1600;
        let gy2 = (gm > 2) ? (gy + 1) : gy;
        let days = (365 * gy) + Math.floor((gy2 + 3) / 4) - Math.floor((gy2 + 99) / 100) + Math.floor((gy2 + 399) / 400) - 80 + gd + g_d_m[gm - 1];
        jy += 33 * Math.floor(days / 12053);
        days %= 12053;
        jy += 4 * Math.floor(days / 1461);
        days %= 1461;

        if (days > 365) {
            jy += Math.floor((days - 1) / 365);
            days = (days - 1) % 365;
        }

        let jm = (days < 186) ? 1 + Math.floor(days / 31) : 7 + Math.floor((days - 186) / 30);
        let jd = 1 + ((days < 186) ? (days % 31) : ((days - 186) % 30));
        
        return { year: jy, month: jm, day: jd };
    },

    getCurrentJalaliDate() {
        const now = new Date();
        const jDate = this.gregorianToJalali(now.getFullYear(), now.getMonth() + 1, now.getDate());
        return `${jDate.year}/${this.toPersianDigits(jDate.month.toString().padStart(2, '0'))}/${this.toPersianDigits(jDate.day.toString().padStart(2, '0'))}`;
    },

    numberToWords(num) {
        if (num === 0) return 'صفر';
        
        const ones = ['', 'یک', 'دو', 'سه', 'چهار', 'پنج', 'شش', 'هفت', 'هشت', 'نه'];
        const teens = ['ده', 'یازده', 'دوازده', 'سیزده', 'چهارده', 'پانزده', 'شانزده', 'هفده', 'هجده', 'نوزده'];
        const tens = ['', '', 'بیست', 'سی', 'چهل', 'پنجاه', 'شصت', 'هفتاد', 'هشتاد', 'نود'];
        const hundreds = ['', 'صد', 'دویست', 'سیصد', 'چهارصد', 'پانصد', 'ششصد', 'هفتصد', 'هشتصد', 'نهصد'];
        
        function convertThree(n) {
            let result = '';
            const h = Math.floor(n / 100);
            const t = Math.floor((n % 100) / 10);
            const o = n % 10;
            
            if (h > 0) {
                result += hundreds[h];
                if (t > 0 || o > 0) result += ' و ';
            }
            
            if (t === 1) {
                result += teens[o];
            } else {
                if (t > 0) {
                    result += tens[t];
                    if (o > 0) result += ' و ';
                }
                if (o > 0) result += ones[o];
            }
            
            return result;
        }
        
        const billion = Math.floor(num / 1000000000);
        const million = Math.floor((num % 1000000000) / 1000000);
        const thousand = Math.floor((num % 1000000) / 1000);
        const remainder = num % 1000;
        
        let words = '';
        
        if (billion > 0) {
            words += convertThree(billion) + ' میلیارد';
            if (million > 0 || thousand > 0 || remainder > 0) words += ' و ';
        }
        
        if (million > 0) {
            words += convertThree(million) + ' میلیون';
            if (thousand > 0 || remainder > 0) words += ' و ';
        }
        
        if (thousand > 0) {
            words += convertThree(thousand) + ' هزار';
            if (remainder > 0) words += ' و ';
        }
        
        if (remainder > 0) {
            words += convertThree(remainder);
        }
        
        return words.trim();
    }
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.persian-num').forEach(el => {
        el.textContent = PersianUtils.toPersianDigits(el.textContent);
    });

    document.querySelectorAll('.format-currency').forEach(el => {
        const amount = el.getAttribute('data-amount');
        const type = el.getAttribute('data-type') || 'rial';
        el.textContent = PersianUtils.formatCurrency(amount, type);
    });

    const dateEl = document.getElementById('current-date-jalali');
    if(dateEl) {
        dateEl.textContent = PersianUtils.getCurrentJalaliDate();
    }
});
