<?php ob_start(); ?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">مدیریت سرنخ‌ها</h1>
        <button onclick="document.getElementById('leadModal').classList.remove('hidden')" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg shadow flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>سرنخ جدید</span>
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex gap-4 flex-wrap">
            <select name="status" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">همه وضعیت‌ها</option>
                <option value="new" <?php echo (isset($_GET['status']) && $_GET['status'] == 'new') ? 'selected' : ''; ?>>جدید</option>
                <option value="contacted" <?php echo (isset($_GET['status']) && $_GET['status'] == 'contacted') ? 'selected' : ''; ?>>تماس گرفته شده</option>
                <option value="qualified" <?php echo (isset($_GET['status']) && $_GET['status'] == 'qualified') ? 'selected' : ''; ?>>احراز صلاحیت</option>
                <option value="lost" <?php echo (isset($_GET['status']) && $_GET['status'] == 'lost') ? 'selected' : ''; ?>>از دست رفته</option>
            </select>
            <select name="source" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">همه منابع</option>
                <option value="website">وبسایت</option>
                <option value="referral">معرفی</option>
                <option value="cold_call">تماس سرد</option>
                <option value="instagram">اینستاگرام</option>
                <option value="linkedin">لینکدین</option>
            </select>
            <button type="submit" class="bg-gray-200 dark:bg-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">فیلتر</button>
        </form>
    </div>

    <!-- Leads Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">عنوان</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">شرکت/مخاطب</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">منبع</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">وضعیت</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">امتیاز</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ارزش تخمینی</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">عملیات</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($leads as $lead): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"><?php echo $lead['title']; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        <?php echo $lead['company_name'] ?? ($lead['contact_name'] ?? '-'); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            <?php 
                            $sources = ['website' => 'وبسایت', 'referral' => 'معرفی', 'cold_call' => 'تماس سرد', 'instagram' => 'اینستاگرام', 'linkedin' => 'لینکدین'];
                            echo $sources[$lead['source']] ?? $lead['source']; 
                            ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            <?php 
                            $statusColors = ['new' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200', 
                                           'contacted' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                           'qualified' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
                                           'lost' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'];
                            echo $statusColors[$lead['status']] ?? 'bg-gray-100 text-gray-800'; 
                            ?>">
                            <?php 
                            $statuses = ['new' => 'جدید', 'contacted' => 'تماس گرفته شده', 'qualified' => 'احراز صلاحیت', 'lost' => 'از دست رفته'];
                            echo $statuses[$lead['status']] ?? $lead['status']; 
                            ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        <div class="flex items-center">
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 ml-2">
                                <div class="bg-<?php echo $lead['score'] > 70 ? 'green' : ($lead['score'] > 40 ? 'yellow' : 'red'); ?>-600 h-2.5 rounded-full" style="width: <?php echo $lead['score']; ?>%"></div>
                            </div>
                            <span class="text-xs"><?php echo $lead['score']; ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 font-mono">
                        <?php echo number_format($lead['estimated_value']); ?> ₽
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="#" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 ml-3">ویرایش</a>
                        <a href="/leads/delete?id=<?php echo $lead['id']; ?>" class="text-red-600 hover:text-red-900 dark:text-red-400" onclick="return confirm('آیا مطمئن هستید؟')">حذف</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="leadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg m-4">
        <div class="p-6 border-b dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-xl font-bold dark:text-white">ایجاد سرنخ جدید</h3>
            <button onclick="document.getElementById('leadModal').classList.add('hidden')" class="text-gray-500 hover:text-red-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="/leads/store" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $this->auth->csrfToken(); ?>">
            <div>
                <label class="block text-sm font-medium dark:text-gray-300 mb-1">عنوان</label>
                <input type="text" name="title" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium dark:text-gray-300 mb-1">منبع</label>
                    <select name="source" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="website">وبسایت</option>
                        <option value="referral">معرفی</option>
                        <option value="cold_call">تماس سرد</option>
                        <option value="instagram">اینستاگرام</option>
                        <option value="linkedin">لینکدین</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium dark:text-gray-300 mb-1">وضعیت اولیه</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="new">جدید</option>
                        <option value="contacted">تماس گرفته شده</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium dark:text-gray-300 mb-1">ارزش تخمینی (ریال)</label>
                <input type="text" name="estimated_value" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="۰">
            </div>
            <div>
                <label class="block text-sm font-medium dark:text-gray-300 mb-1">یادداشت‌ها</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="document.getElementById('leadModal').classList.add('hidden')" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">انصراف</button>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg">ثبت</button>
            </div>
        </form>
    </div>
</div>

<?php $content = ob_get_clean(); include 'views/layouts/layout.php'; ?>
