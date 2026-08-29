<?php ob_start(); ?>

<div class="flex flex-col h-full">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">خط لوله فروش</h2>
        <button onclick="document.getElementById('dealModal').classList.remove('hidden')" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg shadow flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>معامله جدید</span>
        </button>
    </div>

    <!-- Kanban Board -->
    <div class="flex-1 overflow-x-auto overflow-y-hidden">
        <div class="flex h-full gap-4 min-w-max pb-4" id="kanban-board">
            
            <?php foreach ($pipeline as $column): ?>
            <div class="flex flex-col w-80 bg-gray-100 dark:bg-gray-800 rounded-xl shadow-inner h-full">
                <!-- Column Header -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center sticky top-0 bg-gray-100 dark:bg-gray-800 rounded-t-xl z-10">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-<?php echo $column['color']; ?>-500"></span>
                        <h3 class="font-bold text-gray-700 dark:text-gray-200"><?php echo $column['name_fa']; ?></h3>
                        <span class="text-xs text-gray-500 bg-gray-200 dark:bg-gray-700 px-2 py-0.5 rounded-full"><?php echo count($column['deals']); ?></span>
                    </div>
                    <span class="text-xs font-mono text-gray-500"><?php echo number_format($column['total_value']); ?> ₽</span>
                </div>

                <!-- Droppable Area -->
                <div class="flex-1 overflow-y-auto p-2 space-y-3 custom-scrollbar" 
                     data-stage-id="<?php echo $column['id']; ?>" 
                     ondrop="drop(event)" 
                     ondragover="allowDrop(event)">
                    
                    <?php foreach ($column['deals'] as $deal): ?>
                    <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 cursor-move hover:shadow-md transition-shadow" 
                         draggable="true" 
                         ondragstart="drag(event)" 
                         id="deal-<?php echo $deal['id']; ?>"
                         data-value="<?php echo $deal['value']; ?>">
                        
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-semibold text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/30 px-2 py-1 rounded">
                                <?php echo $deal['company_name']; ?>
                            </span>
                            <span class="text-xs text-gray-400"><?php echo $deal['probability']; ?>%</span>
                        </div>

                        <h4 class="font-bold text-gray-800 dark:text-white mb-3"><?php echo $deal['title']; ?></h4>

                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">مبلغ:</span>
                                <span class="font-mono text-gray-700 dark:text-gray-200"><?php echo number_format($deal['value']); ?> ₽</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">بستن در:</span>
                                <span class="font-mono text-gray-700 dark:text-gray-200 dir-ltr text-xs">
                                    <?php if($deal['expected_close_date']): echo date('Y/m/d', strtotime($deal['expected_close_date'])); endif; ?>
                                </span>
                            </div>
                        </div>

                        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-600 flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-xs text-gray-600 dark:text-gray-300">
                                <?php echo mb_substr($deal['owner_name'] ?? 'U', 0, 1); ?>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo $deal['owner_name'] ?? ''; ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>

                </div>
            </div>
            <?php endforeach; ?>

        </div>
    </div>
</div>

<!-- Deal Modal -->
<div id="dealModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto m-4">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center sticky top-0 bg-white dark:bg-gray-800 z-10">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white">ایجاد معامله جدید</h3>
            <button onclick="document.getElementById('dealModal').classList.add('hidden')" class="text-gray-500 hover:text-red-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="/deals/store" method="POST" id="dealForm" class="p-6 space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo $this->auth->csrfToken(); ?>">
            <input type="hidden" name="stage_id" value="1">
            <input type="hidden" name="subtotal_raw" id="subtotal_raw">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">عنوان معامله</label>
                    <input type="text" name="title" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="مثال: فروش نرم‌افزار">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">شرکت طرف حساب</label>
                    <select name="company_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="">انتخاب کنید...</option>
                        <option value="1">تکنو گستران</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">احتمال موفقیت (%)</label>
                    <input type="number" name="probability" min="0" max="100" value="50" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">تاریخ بستن</label>
                    <input type="date" name="close_date" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">نرخ مالیات (%)</label>
                    <input type="number" step="0.1" name="tax_rate" value="9" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>
            </div>

            <div class="border rounded-lg dark:border-gray-600 overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-700 p-3 flex justify-between items-center">
                    <h4 class="font-bold text-gray-700 dark:text-gray-200">اقلام و خدمات</h4>
                    <button type="button" onclick="addLineItem()" class="text-sm text-primary-600 hover:text-primary-700 font-medium">+ افزودن سطر</button>
                </div>
                <div id="lineItemsContainer" class="divide-y divide-gray-100 dark:divide-gray-700">
                    <div class="p-3 grid grid-cols-12 gap-2 items-center line-item">
                        <div class="col-span-5">
                            <input type="text" name="item_desc[]" placeholder="شرح کالا/خدمت" class="w-full text-sm rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div class="col-span-2">
                            <input type="number" name="item_qty[]" value="1" min="1" class="w-full text-sm rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-center">
                        </div>
                        <div class="col-span-3">
                            <input type="text" name="item_price[]" placeholder="قیمت واحد (ریال)" class="w-full text-sm rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-left ltr-input" oninput="calculateTotal()">
                        </div>
                        <div class="col-span-2 text-left font-mono text-sm text-gray-600 dark:text-gray-300 item-total">0</div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-300">جمع کل:</span>
                    <span id="displaySubtotal" class="font-mono font-bold">0 ریال</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-300">تخفیف (<input type="number" name="discount_percent" id="discountInput" value="0" min="0" max="100" class="w-12 text-center mx-1 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800"> %):</span>
                    <span id="displayDiscount" class="font-mono text-red-500">- 0 ریال</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-300">مالیات:</span>
                    <span id="displayTax" class="font-mono text-green-600">0 ریال</span>
                </div>
                <div class="border-t border-gray-300 dark:border-gray-600 pt-2 flex justify-between items-center">
                    <span class="font-bold text-gray-800 dark:text-white">مبلغ نهایی:</span>
                    <span id="displayFinal" class="text-xl font-bold text-primary-600 dark:text-primary-400 font-mono">0 ریال</span>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="document.getElementById('dealModal').classList.add('hidden')" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">انصراف</button>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg shadow-lg transform active:scale-95 transition-all">ثبت معامله</button>
            </div>
        </form>
    </div>
</div>

<script>
let draggedDealId = null;

function drag(ev) {
    draggedDealId = ev.target.id.replace('deal-', '');
    ev.dataTransfer.setData("text", ev.target.id);
    ev.target.classList.add('opacity-50');
}

function allowDrop(ev) {
    ev.preventDefault();
}

function drop(ev) {
    ev.preventDefault();
    const data = ev.dataTransfer.getData("text");
    const card = document.getElementById(data);
    card.classList.remove('opacity-50');

    const column = ev.target.closest('[data-stage-id]');
    if (column) {
        const newStageId = column.getAttribute('data-stage-id');
        column.appendChild(card);

        fetch('/deals/move', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ deal_id: draggedDealId, stage_id: parseInt(newStageId) })
        })
        .then(res => res.json())
        .then(data => {
            if(!data.success) alert('خطا در جابجایی معامله');
        });
    }
}

function addLineItem() {
    const container = document.getElementById('lineItemsContainer');
    const row = document.createElement('div');
    row.className = 'p-3 grid grid-cols-12 gap-2 items-center line-item';
    row.innerHTML = `
        <div class="col-span-5"><input type="text" name="item_desc[]" placeholder="شرح کالا/خدمت" class="w-full text-sm rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
        <div class="col-span-2"><input type="number" name="item_qty[]" value="1" min="1" class="w-full text-sm rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-center"></div>
        <div class="col-span-3"><input type="text" name="item_price[]" placeholder="قیمت واحد (ریال)" class="w-full text-sm rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-left ltr-input" oninput="calculateTotal()"></div>
        <div class="col-span-2 text-left font-mono text-sm text-gray-600 dark:text-gray-300 item-total">0</div>
    `;
    container.appendChild(row);
}

function calculateTotal() {
    let subtotal = 0;
    document.querySelectorAll('.line-item').forEach(row => {
        const qty = parseInt(row.querySelector('input[name="item_qty[]"]').value) || 0;
        const priceStr = typeof PersianUtils !== 'undefined' ? PersianUtils.toEnglishDigits(row.querySelector('input[name="item_price[]"]').value) : row.querySelector('input[name="item_price[]"]').value;
        const price = parseInt(priceStr) || 0;
        const lineTotal = qty * price;
        subtotal += lineTotal;
        row.querySelector('.item-total').textContent = lineTotal.toLocaleString('fa-IR') + ' ریال';
    });

    document.getElementById('subtotal_raw').value = subtotal;
    const discountPercent = parseFloat(document.getElementById('discountInput').value) || 0;
    const taxRate = parseFloat(document.querySelector('input[name="tax_rate"]').value) || 0;

    const discountAmount = subtotal * (discountPercent / 100);
    const afterDiscount = subtotal - discountAmount;
    const taxAmount = afterDiscount * (taxRate / 100);
    const finalAmount = afterDiscount + taxAmount;

    document.getElementById('displaySubtotal').textContent = subtotal.toLocaleString('fa-IR') + ' ریال';
    document.getElementById('displayDiscount').textContent = '- ' + discountAmount.toLocaleString('fa-IR') + ' ریال';
    document.getElementById('displayTax').textContent = taxAmount.toLocaleString('fa-IR') + ' ریال';
    document.getElementById('displayFinal').textContent = finalAmount.toLocaleString('fa-IR') + ' ریال';
}

document.getElementById('discountInput').addEventListener('input', calculateTotal);
</script>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #475569; }
.ltr-input { direction: ltr; text-align: left; }
</style>

<?php $content = ob_get_clean(); include 'views/layouts/layout.php'; ?>
