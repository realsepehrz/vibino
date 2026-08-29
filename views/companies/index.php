<?php ob_start(); ?>

<div class="container mx-auto px-4 py-6">
    <!-- Header & Actions -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">شرکت‌ها و مشتریان</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">مدیریت جامع اطلاعات کسب‌وکارها</p>
        </div>
        <a href="/companies/create" class="bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-lg shadow flex items-center gap-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>شرکت جدید</span>
        </a>
    </div>

    <!-- Advanced Search & Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form action="/companies" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Global Search -->
            <div class="md:col-span-2 relative">
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" 
                       placeholder="جستجو در نام، کد ملی، شهر..." 
                       class="w-full pr-10 pl-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <!-- Status Filter -->
            <select name="status" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2.5 bg-white dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500">
                <option value="">همه وضعیت‌ها</option>
                <option value="active" <?php echo ($_GET['status'] ?? '') === 'active' ? 'selected' : ''; ?>>فعال</option>
                <option value="inactive" <?php echo ($_GET['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>غیرفعال</option>
                <option value="lead" <?php echo ($_GET['status'] ?? '') === 'lead' ? 'selected' : ''; ?>>سرنخ</option>
            </select>

            <!-- Province Filter -->
            <select name="province" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2.5 bg-white dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500">
                <option value="">همه استان‌ها</option>
                <?php foreach ($provinces as $prov): ?>
                    <option value="<?php echo htmlspecialchars($prov); ?>" <?php echo ($_GET['province'] ?? '') === $prov ? 'selected' : ''; ?>><?php echo htmlspecialchars($prov); ?></option>
                <?php endforeach; ?>
            </select>

            <div class="md:col-span-4 flex justify-end gap-3">
                <button type="submit" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg transition-colors">
                    فیلتر
                </button>
                <a href="/companies" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 px-4 py-2">
                    حذف فیلترها
                </a>
            </div>
        </form>
    </div>

    <!-- Companies Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">نام شرکت</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">نوع</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">کد ملی / شناسه</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">شهر / استان</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">وضعیت</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php if (empty($companies)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                <p>هیچ شرکتی یافت نشد</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($companies as $company): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold">
                                        <?php echo mb_substr($company['name'], 0, 1); ?>
                                    </div>
                                    <div class="mr-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($company['name']); ?></div>
                                        <?php if ($company['phone']): ?>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 dir-ltr text-left"><?php echo htmlspecialchars($company['phone']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs rounded-full <?php echo $company['legal_type'] === 'legal' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'; ?>">
                                    <?php echo $company['legal_type'] === 'legal' ? 'حقوقی' : 'حقیقی'; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white font-mono dir-ltr text-left">
                                    <?php echo $company['national_id'] ? htmlspecialchars($company['national_id']) : '-'; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($company['city'] ?? ''); ?></div>
                                <div class="text-xs text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($company['province'] ?? ''); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs rounded-full 
                                    <?php 
                                    switch($company['status']) {
                                        case 'active': echo 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'; break;
                                        case 'inactive': echo 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'; break;
                                        case 'lead': echo 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'; break;
                                        default: echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>">
                                    <?php
                                    $statusLabels = ['active' => 'فعال', 'inactive' => 'غیرفعال', 'lead' => 'سرنخ'];
                                    echo $statusLabels[$company['status']] ?? $company['status'];
                                    ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="/companies/profile?id=<?php echo $company['id']; ?>" class="text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 text-sm font-medium">مشاهده</a>
                                    <span class="text-gray-300 dark:text-gray-600">|</span>
                                    <a href="/companies/edit?id=<?php echo $company['id']; ?>" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">ویرایش</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Placeholder -->
    <?php if (count($companies) > 0): ?>
    <div class="mt-4 flex justify-between items-center text-sm text-gray-500 dark:text-gray-400">
        <div>نمایش <?php echo count($companies); ?> رکورد</div>
        <div class="flex gap-2">
            <button class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50" disabled>قبلی</button>
            <button class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50" disabled>بعدی</button>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); include 'views/layout.php'; ?>