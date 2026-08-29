<?php ob_start(); ?>

<div class="container mx-auto px-4 py-6">
    <!-- Company Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
        <div class="bg-gradient-to-l from-primary-600 to-primary-800 px-6 py-8 text-white">
            <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl font-bold">
                        <?php echo mb_substr($company['name'], 0, 1); ?>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($company['name']); ?></h1>
                        <div class="flex items-center gap-3 mt-2 text-sm opacity-90">
                            <span class="px-2 py-0.5 bg-white/20 rounded-full text-xs">
                                <?php echo $company['legal_type'] === 'legal' ? 'شخصیت حقوقی' : 'شخصیت حقیقی'; ?>
                            </span>
                            <span><?php echo htmlspecialchars($company['city'] ?? ''); ?>، <?php echo htmlspecialchars($company['province'] ?? ''); ?></span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="/companies/edit?id=<?php echo $company['id']; ?>" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        ویرایش
                    </a>
                    <button onclick="confirmDelete()" class="bg-red-500/80 hover:bg-red-500 backdrop-blur-sm px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        حذف
                    </button>
                </div>
            </div>
        </div>

        <!-- Quick Info Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-gray-200 dark:divide-gray-700 border-b border-gray-200 dark:border-gray-700">
            <div class="p-4">
                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">کد ملی / شناسه ثبت</div>
                <div class="font-mono text-sm dir-ltr text-left"><?php echo $company['national_id'] ?? '-'; ?></div>
            </div>
            <div class="p-4">
                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">کد اقتصادی</div>
                <div class="font-mono text-sm dir-ltr text-left"><?php echo $company['economic_code'] ?? '-'; ?></div>
            </div>
            <div class="p-4">
                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">شماره شبا</div>
                <div class="font-mono text-sm dir-ltr text-left"><?php echo $company['sheba'] ?? '-'; ?></div>
            </div>
            <div class="p-4">
                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">وضعیت</div>
                <span class="inline-block px-2 py-1 text-xs rounded-full 
                    <?php 
                    switch($company['status']) {
                        case 'active': echo 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'; break;
                        case 'inactive': echo 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'; break;
                        case 'lead': echo 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'; break;
                    }
                    ?>">
                    <?php
                    $statusLabels = ['active' => 'فعال', 'inactive' => 'غیرفعال', 'lead' => 'سرنخ'];
                    echo $statusLabels[$company['status']] ?? $company['status'];
                    ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Tabbed Interface -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <!-- Tabs Header -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex -mb-px">
                <button onclick="switchTab('details')" id="tab-details" class="tab-button active py-4 px-6 text-sm font-medium border-b-2 border-primary-500 text-primary-600 dark:text-primary-400">
                    اطلاعات تفصیلی
                </button>
                <button onclick="switchTab('contacts')" id="tab-contacts" class="tab-button py-4 px-6 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    مخاطبین (<?php echo count($contacts); ?>)
                </button>
                <button onclick="switchTab('timeline')" id="tab-timeline" class="tab-button py-4 px-6 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    تاریخچه فعالیت‌ها
                </button>
                <button onclick="switchTab('documents')" id="tab-documents" class="tab-button py-4 px-6 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    اسناد و مدارک
                </button>
            </nav>
        </div>

        <!-- Tab Content: Details -->
        <div id="content-details" class="tab-content p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 border-b pb-2">اطلاعات تماس</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">تلفن</dt>
                            <dd class="text-sm font-mono dir-ltr text-left"><?php echo $company['phone'] ?? '-'; ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">وب‌سایت</dt>
                            <dd class="text-sm text-primary-600 dark:text-primary-400">
                                <?php if ($company['website']): ?>
                                    <a href="<?php echo htmlspecialchars($company['website']); ?>" target="_blank" class="hover:underline"><?php echo htmlspecialchars($company['website']); ?></a>
                                <?php else: echo '-'; endif; ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">آدرس</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo nl2br(htmlspecialchars($company['address'] ?? '')); ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">کد پستی</dt>
                            <dd class="text-sm font-mono dir-ltr text-left"><?php echo $company['postal_code'] ?? '-'; ?></dd>
                        </div>
                    </dl>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 border-b pb-2">اطلاعات مالی و بانکی</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">شماره شبا</dt>
                            <dd class="text-sm font-mono dir-ltr text-left"><?php echo $company['sheba'] ?? '-'; ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">کد اقتصادی</dt>
                            <dd class="text-sm font-mono dir-ltr text-left"><?php echo $company['economic_code'] ?? '-'; ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">تاریخ ایجاد</dt>
                            <dd class="text-sm"><?php echo PersianUtils::toPersianDigits(date('Y/m/d', strtotime($company['created_at']))); ?></dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Tab Content: Contacts -->
        <div id="content-contacts" class="tab-content p-6 hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">مخاطبین شرکت</h3>
                <a href="/contacts/create?company_id=<?php echo $company['id']; ?>" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">+ افزودن مخاطب جدید</a>
            </div>
            
            <?php if (empty($contacts)): ?>
                <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <p>هیچ مخاطبی برای این شرکت ثبت نشده است</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach ($contacts as $contact): ?>
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-sm font-bold text-gray-600 dark:text-gray-300">
                                    <?php echo mb_substr($contact['first_name'], 0, 1); ?>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white"><?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?></h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($contact['position'] ?? ''); ?></p>
                                </div>
                            </div>
                            <?php if ($contact['is_primary']): ?>
                                <span class="px-2 py-0.5 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-xs rounded-full">اصلی</span>
                            <?php endif; ?>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">موبایل:</span>
                                <span class="font-mono dir-ltr text-left"><?php echo $contact['mobile'] ?? '-'; ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">ایمیل:</span>
                                <span class="text-primary-600 dark:text-primary-400 truncate max-w-[150px]"><?php echo $contact['email'] ?? '-'; ?></span>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 flex gap-2">
                            <a href="/contacts/edit?id=<?php echo $contact['id']; ?>" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400">ویرایش</a>
                            <span class="text-gray-300 dark:text-gray-600">|</span>
                            <a href="tel:<?php echo $contact['mobile']; ?>" class="text-xs text-gray-600 hover:text-gray-800 dark:text-gray-400">تماس</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tab Content: Timeline -->
        <div id="content-timeline" class="tab-content p-6 hidden">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">تاریخچه فعالیت‌ها</h3>
            <?php if (empty($activities)): ?>
                <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                    <p>هنوز فعالیتی ثبت نشده است</p>
                </div>
            <?php else: ?>
                <div class="flow-root">
                    <ul class="-mb-8">
                        <?php foreach ($activities as $idx => $activity): ?>
                        <li>
                            <div class="relative pb-8">
                                <?php if ($idx !== count($activities) - 1): ?>
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                <?php endif; ?>
                                <div class="relative flex space-x-3 space-x-reverse">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-<?php 
                                            switch($activity['action']) {
                                                case 'created': echo 'green'; break;
                                                case 'updated': echo 'blue'; break;
                                                case 'deleted': echo 'red'; break;
                                                default: echo 'gray';
                                            }
                                        ?>-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <?php if ($activity['action'] === 'created'): ?>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                <?php elseif ($activity['action'] === 'updated'): ?>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                <?php else: ?>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                <?php endif; ?>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4 space-x-reverse">
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                <?php echo htmlspecialchars($activity['description']); ?>
                                                <span class="font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($activity['first_name'] . ' ' . $activity['last_name']); ?></span>
                                            </p>
                                        </div>
                                        <div class="text-right text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                            <time datetime="<?php echo $activity['created_at']; ?>">
                                                <?php echo PersianUtils::toPersianDigits(date('Y/m/d H:i', strtotime($activity['created_at']))); ?>
                                            </time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tab Content: Documents -->
        <div id="content-documents" class="tab-content p-6 hidden">
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">اسناد و مدارک</h3>
                <p class="mt-1 text-sm">این بخش به زودی پیاده‌سازی خواهد شد</p>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" action="/companies/delete" method="POST" class="hidden">
    <input type="hidden" name="id" value="<?php echo $company['id']; ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $this->auth->csrfToken(); ?>">
</form>

<script>
function switchTab(tabName) {
    // Hide all contents
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    // Remove active class from all buttons
    document.querySelectorAll('.tab-button').forEach(el => {
        el.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-400');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected content
    document.getElementById('content-' + tabName).classList.remove('hidden');
    // Activate selected button
    const btn = document.getElementById('tab-' + tabName);
    btn.classList.remove('border-transparent', 'text-gray-500');
    btn.classList.add('border-primary-500', 'text-primary-600', 'dark:text-primary-400');
}

function confirmDelete() {
    if (confirm('آیا از حذف این شرکت اطمینان دارید؟ این عملیات غیرقابل بازگشت است.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>

<?php $content = ob_get_clean(); include 'views/layout.php'; ?>