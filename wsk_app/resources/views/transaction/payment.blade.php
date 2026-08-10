@extends('layout.index')
@section('title','Payment')

@section('navbar')
    <div class="navbar-container flex items-center justify-between w-full gap-4 pe-6">
        <h1 class="text-lg md:text-3xl font-bold">PAYMENT <span class="text-lg text-gray-400">> #{{$transaction->invoice_number}}</span></h1>
        <a href="{{ route('auth.index') }}" class="text-sm w-15 h-15 ms-auto bg-danger-subtle text-danger rounded-full hover:bg-red-300 cursor-pointer outline-0 inline-flex justify-center items-center tutup-modal-order">
            <svg class="w-7 h-7" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
            <span class="sr-only">Close modal</span>
        </a>
    </div>
@endsection

@section('container')
    <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-3 gap-4 p-4 md:p-8">
        <div class="col-span-1 sm:col-span-2 md:col-span-2 flex flex-col gap-4">
            <div class="customer-detail flex items-center justify-between p-4 bg-white rounded-2xl w-full flex-wrap">
                <div class="customer-info flex items-center gap-4 w-full md:w-auto mb-3 md:mb-0">
                    <div class="icon-customer w-12 h-12 rounded-full bg-brand-soft flex items-center justify-center">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <circle cx="12" cy="6" r="4" stroke="#1C274C" stroke-width="1.5"></circle> <path d="M19.9975 18C20 17.8358 20 17.669 20 17.5C20 15.0147 16.4183 13 12 13C7.58172 13 4 15.0147 4 17.5C4 19.9853 4 22 12 22C14.231 22 15.8398 21.8433 17 21.5634" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"></path> </g></svg>
                    </div>
                    <div class="customer-name">
                        <p class="customer-name-text">{{$transaction->customer_name ?? 'Guest Customer'}}</p>
                    </div>
                </div>
                <div class="transaction-invoice mb-3 md:mb-0">
                    <p class="text-base px-3 py-1 bg-brand text-white rounded-full">Invoice No. #{{ $transaction->invoice_number }}</p>
                </div>
                <div class="transaction-date text-right md:text-left w-full md:w-auto">
                    <p class="text-sm text-gray-500">{{ date('d M Y H:i', strtotime($transaction->created_at)) }}</p>
                </div>
            </div>
            <div class="order-detail p-8 bg-white rounded-2xl w-full">
                <p class="text-xl font-bold mb-5">Order Details</p>
                <ul class="order-item-list flex flex-col gap-2 h-[300px] w-full overflow-y-auto">
                    <li class="order-item-header items-center w-full justify-between mb-3 hidden md:flex">
                        <p class="text-base text-gray-500 w-[60%]">Product</p>
                        <p class="text-base text-gray-500 w-[15%]">Quantity</p>
                        <p class="text-base text-gray-500 w-[25%]">Price</p>
                    </li>
                    @foreach ($transaction->orderItem as $item)
                        @php
                            $product = $product->where('uuid', $item->product_id)->first();
                            $imagePath = $product && $product->picture ? asset('storage/products/'.$product->picture) : Vite::asset('resources/img/no_image_available.png');
                        @endphp
                        <li class="order-item flex flex-wrap items-center justify-start md:justify-between mb-3 w-full bg-gray-100 p-3 rounded-lg">
                            <div class="text-base font-medium w-full md:w-[60%] inline-flex items-center">
                                <img src="{{ $imagePath }}" alt="{{ $item->product_name }}" class="w-10 h-10 rounded-full object-cover me-3 inline-block">
                                <div>
                                    <p>{{ $item->product_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $item->note }}</p>
                                </div>
                            </div>
                            
                            <div class="text-sm text-gray-500 w-[15%]">
                                <p class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">x{{ $item->qty }}</p></div>
                            <p class="text-sm w-[75%] md:w-[25%]">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="payment-detail grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="discount-detail col-span-1 bg-white rounded-lg p-8">
                    <div class="inline-flex items-center gap-2 mb-5">
                        <div class="icon-place w-10 h-10 bg-brand-soft rounded-full flex items-center justify-center me-3">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M12 8V21M12 8H7.5C6.83696 8 6.20107 7.73661 5.73223 7.26777C5.26339 6.79893 5 6.16304 5 5.5C5 4.83696 5.26339 4.20107 5.73223 3.73223C6.20107 3.26339 6.83696 3 7.5 3C11 3 12 8 12 8ZM12 8H16.5C17.163 8 17.7989 7.73661 18.2678 7.26777C18.7366 6.79893 19 6.16304 19 5.5C19 4.83696 18.7366 4.20107 18.2678 3.73223C17.7989 3.26339 17.163 3 16.5 3C13 3 12 8 12 8ZM3 14H21M6.2 21H17.8C18.9201 21 19.4802 21 19.908 20.782C20.2843 20.5903 20.5903 20.2843 20.782 19.908C21 19.4802 21 18.9201 21 17.8V11.2C21 10.0799 21 9.51984 20.782 9.09202C20.5903 8.71569 20.2843 8.40973 19.908 8.21799C19.4802 8 18.9201 8 17.8 8H6.2C5.0799 8 4.51984 8 4.09202 8.21799C3.71569 8.40973 3.40973 8.71569 3.21799 9.09202C3 9.51984 3 10.0799 3 11.2V17.8C3 18.9201 3 19.4802 3.21799 19.908C3.40973 20.2843 3.71569 20.5903 4.09202 20.782C4.51984 21 5.07989 21 6.2 21Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                        </div>
                        <div class="text-place">
                            <p class="text-gray-500 font-black">Discount</p>
                            <p class="text-gray-400 text-sm mb-2">Enter Discount Amount to apply Discount</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-2 flex-col">
                        <input type="number" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand" placeholder="Enter discount Amount" value="{{ $transaction->discount }}">
                        <button class="px-4 py-3 bg-brand-light text-white rounded-lg apply-discount">Apply Discount</button>
                    </div>
                </div>
                <div class="payment-summary col-span-1 bg-white rounded-lg p-8">
                    <div class="flex justify-between mb-2">
                        <p class="text-gray-500 font-black">Subtotal</p>
                        <p class="font-bold">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex justify-between mb-2">
                        <p class="text-gray-500">Discount</p>
                        <p class="font-medium discount-place">Rp {{ number_format($transaction->discount, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex justify-between mb-2">
                        <p class="text-gray-500">Tax</p>
                        <p class="font-medium tax-place">Rp {{ number_format($transaction->tax, 0, ',', '.') }}</p>
                    </div>
                    <hr class="my-3">
                    <div class="flex justify-between mb-2">
                        <p class="font-bold">Total</p>
                        <p class="font-bold text-lg total-place">Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-1">
            <div class="payment-methods w-full p-8 bg-neutral-primary rounded-2xl">
                <p class="text-lg font-bold">Payment Methods</p>
                <ul class="select-none grid w-full gap-4 md:grid-cols-1 mt-3">
                    <li>
                        <input type="radio" id="cash" value="cash" name="payment_method" class="hidden peer" @if ($transaction->paid_method == 'cash') checked @endif>
                        <label for="cash" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border-1 border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                            <div class="inline-flex items-center gap-2">
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M7 13C7 11.1144 7 10.1716 7.58579 9.58579C8.17157 9 9.11438 9 11 9H14H17C18.8856 9 19.8284 9 20.4142 9.58579C21 10.1716 21 11.1144 21 13V14V15C21 16.8856 21 17.8284 20.4142 18.4142C19.8284 19 18.8856 19 17 19H14H11C9.11438 19 8.17157 19 7.58579 18.4142C7 17.8284 7 16.8856 7 15V14V13Z" stroke="#323232" stroke-width="2" stroke-linejoin="round"></path> <path d="M7 15V15C5.11438 15 4.17157 15 3.58579 14.4142C3.58579 14.4142 3.58579 14.4142 3.58579 14.4142C3 13.8284 3 12.8856 3 11L3 9C3 7.11438 3 6.17157 3.58579 5.58579C4.17157 5 5.11438 5 7 5L13 5C14.8856 5 15.8284 5 16.4142 5.58579C17 6.17157 17 7.11438 17 9V9" stroke="#323232" stroke-width="2" stroke-linejoin="round"></path> <path d="M16 14C16 15.1046 15.1046 16 14 16C12.8954 16 12 15.1046 12 14C12 12.8954 12.8954 12 14 12C15.1046 12 16 12.8954 16 14Z" stroke="#323232" stroke-width="2"></path> </g></svg>
                                <div class="w-full font-medium mb-1">Cash</div>
                            </div>
                        </label>
                    </li>
                    <li>
                        <input type="radio" id="qris" value="qris" name="payment_method" class="hidden peer" @if ($transaction->paid_method == 'qris') checked @endif>
                        <label for="qris" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border-1 border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                            <div class="inline-flex items-center gap-2">
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M15 6C15 5.06812 15 4.60218 15.1522 4.23463C15.3552 3.74458 15.7446 3.35523 16.2346 3.15224C16.6022 3 17.0681 3 18 3C18.9319 3 19.3978 3 19.7654 3.15224C20.2554 3.35523 20.6448 3.74458 20.8478 4.23463C21 4.60218 21 5.06812 21 6C21 6.93188 21 7.39782 20.8478 7.76537C20.6448 8.25542 20.2554 8.64477 19.7654 8.84776C19.3978 9 18.9319 9 18 9C17.0681 9 16.6022 9 16.2346 8.84776C15.7446 8.64477 15.3552 8.25542 15.1522 7.76537C15 7.39782 15 6.93188 15 6Z" stroke="#323232" stroke-width="2" stroke-linejoin="round"></path> <path d="M3 6C3 5.06812 3 4.60218 3.15224 4.23463C3.35523 3.74458 3.74458 3.35523 4.23463 3.15224C4.60218 3 5.06812 3 6 3C6.93188 3 7.39782 3 7.76537 3.15224C8.25542 3.35523 8.64477 3.74458 8.84776 4.23463C9 4.60218 9 5.06812 9 6C9 6.93188 9 7.39782 8.84776 7.76537C8.64477 8.25542 8.25542 8.64477 7.76537 8.84776C7.39782 9 6.93188 9 6 9C5.06812 9 4.60218 9 4.23463 8.84776C3.74458 8.64477 3.35523 8.25542 3.15224 7.76537C3 7.39782 3 6.93188 3 6Z" stroke="#323232" stroke-width="2" stroke-linejoin="round"></path> <path d="M3 18C3 17.0681 3 16.6022 3.15224 16.2346C3.35523 15.7446 3.74458 15.3552 4.23463 15.1522C4.60218 15 5.06812 15 6 15C6.93188 15 7.39782 15 7.76537 15.1522C8.25542 15.3552 8.64477 15.7446 8.84776 16.2346C9 16.6022 9 17.0681 9 18C9 18.9319 9 19.3978 8.84776 19.7654C8.64477 20.2554 8.25542 20.6448 7.76537 20.8478C7.39782 21 6.93188 21 6 21C5.06812 21 4.60218 21 4.23463 20.8478C3.74458 20.6448 3.35523 20.2554 3.15224 19.7654C3 19.3978 3 18.9319 3 18Z" stroke="#323232" stroke-width="2" stroke-linejoin="round"></path> <path d="M12 3V6" stroke="#323232" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M18 18H15" stroke="#323232" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M21 15H18" stroke="#323232" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M9 12L3 12" stroke="#323232" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M21 12L13 12V12C12.4477 12 12 11.5523 12 11V11L12 9" stroke="#323232" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M14.5 21L20 21V21C20.5523 21 21 20.5523 21 20V20L21 18" stroke="#323232" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M12 21L12 16.2L12 16C12 15.4477 12.4477 15 13 15V15L15 15" stroke="#323232" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                                <div class="w-full font-medium mb-1">QRIS</div>
                            </div>
                        </label>
                    </li>
                    <li>
                        <input type="radio" id="transfer" value="transfer" name="payment_method" class="hidden peer"  @if ($transaction->paid_method == 'transfer') checked @endif>
                        <label for="transfer" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border-1 border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                            <div class="inline-flex items-center gap-2">
                                <svg class="w-10 h-10" fill="#000000" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M75.249 184.32h92.805c11.311 0 20.48-9.169 20.48-20.48s-9.169-20.48-20.48-20.48H75.249c-11.311 0-20.48 9.169-20.48 20.48s9.169 20.48 20.48 20.48z"></path><path d="M188.534 256.645V163.84c0-11.311-9.169-20.48-20.48-20.48s-20.48 9.169-20.48 20.48v92.805c0 11.311 9.169 20.48 20.48 20.48s20.48-9.169 20.48-20.48z"></path><path d="M178.331 844.362c-87.4-87.4-137.372-205.543-137.372-331.641 0-119.519 44.857-231.97 124.29-318.029 7.672-8.312 7.153-21.268-1.159-28.94s-21.268-7.153-28.94 1.159C48.801 260.463-.001 382.804-.001 512.721c0 137.072 54.364 265.599 149.369 360.604 7.998 7.998 20.965 7.998 28.963 0s7.998-20.965 0-28.963zm769.796-5.999h-92.805c-11.311 0-20.48 9.169-20.48 20.48s9.169 20.48 20.48 20.48h92.805c11.311 0 20.48-9.169 20.48-20.48s-9.169-20.48-20.48-20.48z"></path><path d="M834.842 766.038v92.805c0 11.311 9.169 20.48 20.48 20.48s20.48-9.169 20.48-20.48v-92.805c0-11.311-9.169-20.48-20.48-20.48s-20.48 9.169-20.48 20.48z"></path><path d="M845.045 178.322c87.4 87.4 137.372 205.543 137.372 331.641 0 119.519-44.857 231.97-124.29 318.029-7.672 8.312-7.153 21.268 1.159 28.94s21.268 7.153 28.94-1.159c86.349-93.552 135.151-215.893 135.151-345.81 0-137.072-54.364-265.599-149.369-360.604-7.998-7.998-20.965-7.998-28.963 0s-7.998 20.965 0 28.963zm-87.918 495.217c16.962 0 30.72-13.758 30.72-30.72V379.047c0-16.968-13.754-30.72-30.72-30.72H268.351c-16.966 0-30.72 13.752-30.72 30.72v263.772c0 16.962 13.758 30.72 30.72 30.72h488.776zm0 40.96H268.351c-39.583 0-71.68-32.097-71.68-71.68V379.047c0-39.591 32.094-71.68 71.68-71.68h488.776c39.586 0 71.68 32.089 71.68 71.68v263.772c0 39.583-32.097 71.68-71.68 71.68z"></path><path d="M586.34 510.932c0-40.651-32.952-73.605-73.605-73.605-40.644 0-73.595 32.956-73.595 73.605s32.951 73.605 73.595 73.605c40.653 0 73.605-32.954 73.605-73.605zm40.96 0c0 63.272-51.29 114.565-114.565 114.565-63.267 0-114.555-51.295-114.555-114.565s51.288-114.565 114.555-114.565c63.276 0 114.565 51.293 114.565 114.565z"></path></g></svg>
                                <div class="w-full font-medium mb-1">Transfer</div>
                            </div>
                        </label>
                    </li>
                </ul>
            </div>
            @php
                if($transaction->total_paid != "" && $transaction->total_paid != null) {
                    $change = $transaction->total_paid - $transaction->total;
                    $change = number_format($change,0,',','.');
                } else {
                    $change = 0 - $transaction->total;
                    $change = number_format($change,0,',','.');
                }
            @endphp
            <div class="payment-methods-detail w-full p-8 bg-neutral-primary rounded-2xl mt-4 flex flex-col">
                <input type="hidden" id="total_without_format" value="{{ $transaction->total }}">
                <label for="jumlah_bayar" class="text-lg font-bold">Amount</label>
                <p class="text-lg font-bold mt-2 total-place">Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>
                <label for="jumlah_yang_dibayar" class="text-lg font-bold mt-5">Paid Amount</label>
                <input type="number" id="jumlah_yang_dibayar" name="jumlah_yang_dibayar" class="bg-neutral-primary border-1 border-default rounded-base p-2 focus:outline-none focus:ring-2 focus:ring-brand" placeholder="Enter payment amount" value="{{ $transaction->total_paid }}">
                <label for="jumlah_yang_dibayar" class="text-lg font-bold mt-5">Change</label>
                <p class="text-lg font-bold mt-2 change-form">Rp {{ $change }}</p>

                <!-- Bluetooth Connection Panel -->
                <div class="p-4 bg-white border border-gray-200 rounded-xl mt-4 flex flex-col gap-3 text-xs shadow-sm">
                    <div class="flex items-center justify-between flex-wrap gap-2 border-b border-gray-100 pb-2">
                        <span class="font-bold text-gray-700 uppercase tracking-wider text-[10px]">Bluetooth Printer</span>
                        <span id="bt-device-name" class="font-semibold text-emerald-600 hidden truncate max-w-[130px]"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" id="btn-toggle-bluetooth" class="px-3 py-2 bg-brand hover:bg-brand-strong text-white font-bold rounded-lg flex items-center gap-1.5 transition-all cursor-pointer shadow-sm w-full justify-center text-[11px] outline-none">
                            <i class="fab fa-bluetooth text-[11px]"></i> <span id="bt-status-text">Hubungkan Bluetooth</span>
                        </button>
                    </div>
                    
                    <div class="flex items-center justify-between gap-1.5 pt-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Metode Cetak:</label>
                        <select id="print-method-select" class="px-2 py-1 bg-white border border-brand rounded-lg text-[11px] font-bold focus:outline-none cursor-pointer">
                            <option value="browser" selected>Browser Print (HTML)</option>
                            <option value="bluetooth">Direct Bluetooth</option>
                            <option value="rawbt">RawBT (Android)</option>
                        </select>
                    </div>
                </div>

                <button type="button" class="bg-fg-success text-white py-2 px-4 rounded-base hover:bg-brand-hover focus:outline-none focus:ring-2 focus:ring-success cursor-pointer mt-4" id="print-check-receipt">Print Check Receipt</button>
                <button type="button" class="bg-brand text-white py-2 px-4 rounded-base hover:bg-brand-hover focus:outline-none focus:ring-2 focus:ring-brand cursor-pointer mt-2" id="process-payment">Process Payment</button>
            </div>

        </div>
    </div>
    <div id="modal-payment-success" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full z-99">
        <div class="relative p-4 w-full max-w-xl max-h-[90%]">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-3 md:p-3 rounded-t text-start sm:text-center">
                    <h3 class="text-lg font-semibold text-dark-soft w-full">
                    </h3>
                    <div class="button-place flex gap-1">
                        
                        <button type="button" class="text-sm w-9 h-9 ms-auto bg-danger-subtle text-danger rounded-full hover:bg-red-300 cursor-pointer outline-0 inline-flex justify-center items-center tutup-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                </div>
                <!-- Modal body -->
                <div>
                    <div class="grid grid-cols-1 p-4 w-full">
                        <div class="col-span-1 w-full">
                            <div class="image-place w-30 h-30 mx-auto">
                                <img src="{{ Vite::asset('resources/img/cash_payment.gif') }}" class="w-30 h-30 object-contain image-payment" />
                            </div>
                            <div class="text-place w-full">
                                <h3 class="text-2xl font-bold text-center">PAYMENT SUCCESSFULLY</h3>
                            </div>
                            <div class="payment-detail w-full px-5 mt-5">
                                <ul class="flex flex-wrap w-full gap-3">
                                    <li class="flex justify-between w-full">
                                        <span>Invoice</span> <span class="invoice-name"></span>
                                    </li>
                                    <li class="flex justify-between w-full">
                                        <span>Order Type</span> <span class="order-type"></span>
                                    </li>
                                    <li class="flex justify-between w-full">
                                        <span>Total</span> <span class="total-detail"></span>
                                    </li>
                                    <li class="flex justify-between w-full">
                                        <span>Paid</span> <span class="paid-detail"></span>
                                    </li>
                                    <li class="flex justify-between w-full">
                                        <span>Change</span> <span class="change-detail"></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-span-1 w-full gap-1 mt-2">
                            <button class="bg-success w-full text-white rounded-base px-4 py-2 mt-2 hover:bg-success-strong cursor-pointer print-final-receipt"><i class="fas fa-print"></i> Print Receipt</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div id="printreceiptcheck" class="hidden">
        <!-- Content to be printed -->
        <h1 style="text-align: center; margin:0;padding:0" class="print-receipt">CHECK</h1>
        <h1 style="text-align: center; margin:0;padding:0" class="print-receipt" id="receipt-meja"></h1>
        <p style="text-align: center; margin:0;padding:0" class="print-receipt">=======================</p>
        <p style="margin:0;padding:0;font-size:13px" class="print-receipt">Date : <span id="receipt-date">{{ date('Y-m-d H:i:s') }}</span></p>
        <p style="text-align: center; margin:0;padding:0" class="print-receipt">=======================</p>
        <p style="margin:0;padding:0;font-size:13px" class="print-receipt">No Inv : <span id="receipt-invoice-number"></span></p>
        <p style="margin:0;padding:0;font-size:13px" class="print-receipt">Name : <span id="receipt-customer-name"></span></p>
        <p style="margin:0;padding:0;font-size:13px" class="print-receipt">Order Type : <span id="receipt-order-type"></span></p>
        <p style="text-align: center; margin:0;padding:0; margin-bottom:10px;" class="print-receipt">=======================</p>
        <div id="receipt-items">

        </div>
        <p style="text-align: center; margin:0;padding:0; margin-bottom:10px;" class="print-receipt">=======================</p>
        <div style="margin:0; margin-top: -25px; padding: 0; font-size: 13px; display: flex; justify-content: space-between; " class="print-receipt">
            <p class="item-subtotal" style="font-style: italic">Subtotal :</p>
            <p class="item-subtotal-amount" id="receipt-subtotal" style="font-size: 13px; margin-right:5px; font-weight:bold; align-self: flex-end"></p>
        </div>
        <div style="margin:0; margin-top: -25px; padding: 0; font-size: 13px; display: flex; justify-content: space-between; " class="print-receipt">
            <p class="item-tax" style="font-style: italic">Tax :</p>
            <p class="item-tax-amount" id="receipt-tax" style="font-size: 13px; margin-right:5px; font-weight:bold; align-self: flex-end"></p>
        </div>
        <div style="margin:0; margin-top: -25px; padding: 0; font-size: 13px; display: flex; justify-content: space-between; " class="print-receipt">
            <p class="item-discount" style="font-style: italic">Discount :</p>
            <p class="item-discount-amount" id="receipt-discount" style="font-size: 13px; margin-right:5px; font-weight:bold; align-self: flex-end"></p>
        </div>
        <div style="margin:0; margin-top: -25px; padding: 0; font-size: 13px; display: flex; justify-content: space-between; " class="print-receipt">
            <p class="item-total" style="font-style: italic">Total :</p>
            <p class="item-total-amount" id="receipt-total" style="font-size: 13px; margin-right:5px; font-weight:bold; align-self: flex-end"></p>
        </div>
        <p style="page-break-after: auto !important"></p>

    </div>
    <iframe id="printreceiptcheck-withprice" name="printreceiptcheck" class="hidden" style="font-family: Georgia, 'Times New Roman', Times, serif">
    </iframe>
    <div id="printreceiptpayment" class="hidden">
        <!-- Content to be printed -->

        <img id="payment-logo" src="{{ Vite::asset('resources/img/logo-perusahaan.png') }}" style="width: 50%; height: 50px; margin: 0 auto; display: block;"/>
        <h1 id="payment-name" style="text-align: center; font-size:24px; margin:0;padding:0" class="print-receipt">{{ $resName }}</h1>
        <p id="payment-alamat" style="text-align: center; font-size:11px; margin:0;padding:0" class="print-receipt">{{ $resLocation }}</p>
        <p style="text-align: center; margin:0;padding:0" class="print-receipt">=======================</p>
        <p style="margin:0;padding:0;font-size:13px" class="print-receipt">Paid At : <span id="payment-date"></span></p>
        <p style="margin:0;padding:0;font-size:13px" class="print-receipt">No Inv : <span id="payment-invoice-number"></span></p>
        <p style="margin:0;padding:0;font-size:13px" class="print-receipt">Name : <span id="payment-customer-name"></span></p>
        <p style="margin:0;padding:0;font-size:13px" class="print-receipt">Order Type : <span id="payment-order-type"></span></p>
        <p style="margin:0;padding:0;font-size:13px" class="print-receipt">Table : <span id="payment-table"></span></p>
        <p style="margin:0;padding:0;font-size:13px" class="print-receipt">Kasir : <span id="payment-kasir"></span></p>
        <p style="text-align: center; margin:0;padding:0; margin-bottom:10px;" class="print-receipt">=======================</p>
        <div id="payment-items">

        </div>
        <p style="text-align: center; margin:0;padding:0; margin-bottom:10px;" class="print-receipt">=======================</p>
        <div style="margin:0; margin-top: -25px; padding: 0; font-size: 13px; display: flex; justify-content: space-between; " class="print-receipt">
            <p class="item-subtotal" style="font-style: italic">Subtotal :</p>
            <p class="item-subtotal-amount" id="payment-subtotal" style="font-size: 13px; margin-right:5px; font-weight:bold; align-self: flex-end"></p>
        </div>
        <div style="margin:0; margin-top: -25px; padding: 0; font-size: 13px; display: flex; justify-content: space-between; " class="print-receipt">
            <p class="item-tax" style="font-style: italic">Tax :</p>
            <p class="item-tax-amount" id="payment-tax" style="font-size: 13px; margin-right:5px; font-weight:bold; align-self: flex-end"></p>
        </div>
        <div style="margin:0; margin-top: -25px; padding: 0; font-size: 13px; display: flex; justify-content: space-between; " class="print-receipt">
            <p class="item-discount" style="font-style: italic">Discount :</p>
            <p class="item-discount-amount" id="payment-discount" style="font-size: 13px; margin-right:5px; font-weight:bold; align-self: flex-end"></p>
        </div>
        <div style="margin:0; margin-top: -25px; padding: 0; font-size: 13px; display: flex; justify-content: space-between; " class="print-receipt">
            <p class="item-total" style="font-style: italic">Total :</p>
            <p class="item-total-amount" id="payment-total" style="font-size: 13px; margin-right:5px; font-weight:bold; align-self: flex-end"></p>
        </div>
        <p style="text-align: center; margin:0;padding:0; margin-bottom:10px;" class="print-receipt">=======================</p>
        <div style="margin:0; margin-top: -25px; padding: 0; font-size: 13px; display: flex; justify-content: space-between; " class="print-receipt">
            <p class="item-paid" style="font-style: italic">Paid :</p>
            <p class="item-paid-amount" id="payment-paid" style="font-size: 13px; margin-right:5px; font-weight:bold; align-self: flex-end"></p>
        </div>
        <div style="margin:0; margin-top: -25px; padding: 0; font-size: 13px; display: flex; justify-content: space-between; " class="print-receipt">
            <p class="item-change" style="font-style: italic">Change :</p>
            <p class="item-change-amount" id="payment-change" style="font-size: 13px; margin-right:5px; font-weight:bold; align-self: flex-end"></p>
        </div>
        <p style="text-align: center; margin:0;padding:0; margin-bottom:5px;" class="print-receipt">=======================</p>
        <p style="text-align: center; margin:0;padding:0;" class="print-receipt">Terima kasih</p>
        <p style="text-align: center; margin:0;padding:0;" class="print-receipt">Atas Kunjungan Anda</p>
        <p style="page-break-after: auto !important"></p>

    </div>
    <iframe id="printreceiptpayment-check" name="printreceiptpayment" style="visibility: hidden; position: absolute; left: -9999px;">
    </iframe>
    <script type="module">
        const osInstance = OverlayScrollbars(document.querySelector('.order-item-list'), {});
        const $targetEl = document.getElementById("modal-payment-success");
        // options with default values
        const options = {
            placement: "center",
            backdrop: "dynamic",
            backdropClasses: "bg-gray-900/50 fixed inset-0 z-40",
            closable: true,
        };
        const modal = new Modal($targetEl,options);

         $('.apply-discount').on('click', function() {
            const discountValue = $(this).prev('input').val();
            if(discountValue === '' || isNaN(discountValue)) {
                oAlert('orange','Warning','Please enter a valid discount amount');
            }
            loading();
            var url = "{{ route('transaction.payment.discount',$transaction->uuid) }}";
            $.ajax({
                method: 'POST',
                url: url,
                data: {
                    'discount': discountValue,
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    if(data.success == true) {
                        oAlert('green','Success',data.message);
                        removeLoading();
                        $('.discount-place').text('Rp ' + data.discount_formatted);
                        $('.total-place').text('Rp ' + data.total_formatted);
                        $('.tax-place').text('Rp '+ data.tax_formatted);
                        $('#total_without_format').val(data.total);

                        //Change Discount
                        const paidAmount = parseFloat($('input[name="jumlah_yang_dibayar"]').val());
                        const totalAmount = parseFloat($('#total_without_format').val());
                        if(isNaN(paidAmount)) {
                            $('.payment-methods-detail .change-form').text('Rp ' + addCommas(0));
                            return;
                        }
                        const change = paidAmount - totalAmount;
                        $('.payment-methods-detail .change-form').text('Rp ' + addCommas(change));
                    }
                },
                error: function(data) {
                    console.log(data.responseJSON.message);
                }
            })
         });
         $('input[name="jumlah_yang_dibayar"]').on('change',function() {
            const paidAmount = parseFloat($(this).val());
            const totalAmount = parseFloat($('#total_without_format').val());
            if(isNaN(paidAmount)) {
                $('.payment-methods-detail .change-form').text('Rp ' + addCommas(0));
                return;
            }
            const change = paidAmount - totalAmount;
            $('.payment-methods-detail .change-form').text('Rp ' + addCommas(change));
         });
          // Bluetooth — auto-reconnect saat halaman dimuat
          initBluetoothUI();

          // Bluetooth Printer Toggler in Payment Page
          $('#btn-toggle-bluetooth').on('click', async function() {
              if (window.bluetoothPrinterInstance.isConnected()) {
                  window.bluetoothPrinterInstance.disconnect();
                  if (window._setBtUI) window._setBtUI(false);
                  oAlert('orange', 'Disconnected', 'Printer Bluetooth terputus.');
              } else {
                  loading();
                  try {
                      await window.bluetoothPrinterInstance.connect();
                      const deviceName = window.bluetoothPrinterInstance.device.name || 'BT Printer';
                      if (window._setBtUI) window._setBtUI(true, deviceName);
                      removeLoading();
                      oAlert('green', 'Connected', `Terhubung ke ${deviceName}`);
                  } catch (e) {
                      removeLoading();
                      oAlert('red', 'Error', 'Gagal menghubungkan printer bluetooth atau dibatalkan.');
                  }
              }
          });

          // Print Check Receipt (Before checkout payment)
          $('#print-check-receipt').on('click', function() {
              loading();
              var url = "{{ route('transaction.print.check', ':id') }}";
              url = url.replace(':id', '{{ $transaction->uuid }}');
              
              $.ajax({
                  method: 'GET',
                  url: url,
                  success: async function(data) {
                      removeLoading();
                      if(data.success == true) {
                          const method = $('#print-method-select').val();
                          
                          if (method === 'bluetooth') {
                              if (!window.bluetoothPrinterInstance.isConnected()) {
                                  oAlert('orange', 'Warning', 'Printer Bluetooth belum terhubung. Silakan hubungkan terlebih dahulu.');
                                  return;
                              }
                              try {
                                  loading();
                                  const bytes = buildEscPosReceipt(data);
                                  await window.bluetoothPrinterInstance.print(bytes);
                                  removeLoading();
                                  oAlert('green', 'Printed', 'Struk berhasil dicetak via Bluetooth.');
                              } catch (e) {
                                  removeLoading();
                                  oAlert('red', 'Error', 'Gagal mengirim data ke printer Bluetooth.');
                              }
                          } else if (method === 'rawbt') {
                              try {
                                  const bytes = buildEscPosReceipt(data);
                                  window.printViaRawBT(bytes);
                                  oAlert('green', 'Success', 'Struk dikirim ke RawBT.');
                              } catch (e) {
                                  oAlert('red', 'Error', 'Gagal memicu RawBT.');
                              }
                          } else {
                              printHtmlReceipt(data);
                          }
                      } else {
                          oAlert('red', 'Error', 'Gagal memuat data tagihan.');
                      }
                  },
                  error: function(data) {
                      removeLoading();
                      oAlert('red', 'Error', 'Gagal menghubungi server untuk mengambil data struk.');
                  }
              });
          });
         $('#process-payment').on('click',function() {
            var payment_method = $("input[name='payment_method']:checked").val();
            var payment_ammount = $('input[name="jumlah_yang_dibayar"]').val();
            if(payment_method == '' && payment_ammount == '') {
                oAlert("orange","Warning","Payment Method and Amount cannot empty");
            } else {
                loading();
                var url = "{{ route('transaction.payment.finalize',':id') }}";
                url = url.replace(':id',"{{ $transaction->uuid }}");
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {method : payment_method, amount : payment_ammount},
                    headers: {'X-CSRF-TOKEN' : "{{ csrf_token() }}"},
                    success: function(data) {
                        if(data.success == true) {

                            var transaction = data.transaction;
                            if(transaction.paid_method == 'qris') {
                                var image = "{{ Vite::asset('resources/img/qris_payment.gif') }}";
                            } else if(transaction.paid_method == 'transfer') {
                                var image = "{{ Vite::asset('resources/img/credit_card_payment.gif') }}";
                            } else {
                                var image = "{{ Vite::asset('resources/img/cash_payment.gif') }}";
                            }

                            var change = parseFloat(transaction.total_paid) - parseFloat(transaction.total);


                            $('.image-payment').attr('src',image);
                            $('.invoice-name').text(transaction.invoice_number);
                            $('.order-type').text(transaction.order_type);
                            $('.total-detail').text("Rp. "+addCommas(transaction.total));
                            $('.paid-detail').text("Rp. "+addCommas(transaction.total_paid));
                            $('.change-detail').text("Rp. "+addCommas(change));

                            modal.toggle();
                            removeLoading();

                            $('.tutup-modal').on('click',function() {
                                modal.hide();
                            });

                        }
                    },
                    error: function(data) {
                        console.log(data.responseJSON.message);
                    }
                })
            }

         });
          $('.print-final-receipt').on('click', function() {
              loading();
              var url = "{{ route('transaction.print.payment', ':id') }}";
              url = url.replace(':id', '{{ $transaction->uuid }}');
              
              $.ajax({
                  method: 'GET',
                  url: url,
                  success: async function(data) {
                      removeLoading();
                      if(data.success == true) {
                          const method = $('#print-method-select').val();
                          
                          if (method === 'bluetooth') {
                              if (!window.bluetoothPrinterInstance.isConnected()) {
                                  oAlert('orange', 'Warning', 'Printer Bluetooth belum terhubung. Silakan hubungkan terlebih dahulu.');
                                  return;
                              }
                              try {
                                  loading();
                                  const bytes = buildEscPosReceipt(data);
                                  await window.bluetoothPrinterInstance.print(bytes);
                                  removeLoading();
                                  oAlert('green', 'Printed', 'Struk berhasil dicetak via Bluetooth.');
                              } catch (e) {
                                  removeLoading();
                                  oAlert('red', 'Error', 'Gagal mengirim data ke printer Bluetooth.');
                              }
                          } else if (method === 'rawbt') {
                              try {
                                  const bytes = buildEscPosReceipt(data);
                                  window.printViaRawBT(bytes);
                                  oAlert('green', 'Success', 'Struk dikirim ke RawBT.');
                              } catch (e) {
                                  oAlert('red', 'Error', 'Gagal memicu RawBT.');
                              }
                          } else {
                              printHtmlReceipt(data);
                          }
                      } else {
                          oAlert('red', 'Error', 'Gagal memuat data struk pembayaran.');
                      }
                  },
                  error: function(data) {
                      removeLoading();
                      oAlert('red', 'Error', 'Gagal menghubungi server untuk mengambil data struk.');
                  }
              });
          });

          // Helper: Convert transaction data to ESC/POS binary bytes
          function buildEscPosReceipt(data, noPrice = false) {
              const tx = data.transaction;
              const items = tx.order_item || [];
              const res = data.restaurant || {};
              const cashier = data.user || 'Kasir';

              const encoder = new window.EscPosEncoder();
              encoder.initialize();

              // Header
              encoder.alignCenter();
              encoder.bold(true);
              encoder.doubleSize(true);
              if (noPrice) {
                  encoder.line('KITCHEN CHECK');
              } else {
                  encoder.line(res.name || 'POS KASIR');
              }
              encoder.doubleSize(false);
              encoder.bold(false);
              
              if (!noPrice && res.location) {
                  encoder.line(res.location);
              }
              encoder.line('================================');

              // Invoice details
              encoder.alignLeft();
              encoder.line(`Tanggal: ${moment(tx.paid_at || tx.created_at).format('DD/MM/YYYY HH:mm')}`);
              encoder.line(`Invoice: #${tx.invoice_number}`);
              encoder.line(`Meja   : ${tx.order_type === 'take_away' ? 'Take Away' : (tx.table ? tx.table.name : 'Dine In')}`);
              encoder.line(`Kasir  : ${cashier}`);
              encoder.line('--------------------------------');

              // Sales Items
              items.forEach(elem => {
                  encoder.bold(true);
                  encoder.line(elem.product_name || 'Item');
                  encoder.bold(false);

                  if (elem.note) {
                      encoder.line(` * Note: ${elem.note}`);
                  }

                  if (noPrice) {
                      encoder.line(`Qty: ${elem.qty}`);
                  } else {
                      const qtyPrice = `${elem.qty} x Rp ${addCommas(elem.price || (elem.subtotal/elem.qty))}`;
                      const itemTotal = `Rp ${addCommas(elem.subtotal)}`;
                      encoder.twoColumnRow(qtyPrice, itemTotal);
                  }
              });

              encoder.line('--------------------------------');

              if (!noPrice) {
                  const subtotalStr = `Rp ${addCommas(tx.subtotal || tx.total)}`;
                  const taxStr = `Rp ${addCommas(tx.tax || 0)}`;
                  const discStr = `Rp ${addCommas(tx.discount || 0)}`;
                  const totalStr = `Rp ${addCommas(tx.total)}`;
                  const paidStr = `Rp ${addCommas(tx.total_paid || 0)}`;
                  const changed = tx.total_paid > 0 ? (tx.total_paid - tx.total) : 0;
                  const changedStr = `Rp ${addCommas(changed)}`;

                  encoder.twoColumnRow('Subtotal', subtotalStr);
                  encoder.twoColumnRow('Pajak (10%)', taxStr);
                  if (tx.discount > 0) {
                      encoder.twoColumnRow('Diskon', '-' + discStr);
                  }
                  encoder.line('--------------------------------');
                  
                  encoder.bold(true);
                  encoder.twoColumnRow('TOTAL', totalStr);
                  encoder.bold(false);
                  
                  encoder.line('--------------------------------');
                  encoder.twoColumnRow(`Dibayar (${tx.paid_method || 'CASH'})`, paidStr);
                  encoder.twoColumnRow('Kembalian', changedStr);

                  encoder.line('================================');
              }
              
              // Footer
              encoder.alignCenter();
              encoder.line(noPrice ? 'Sajian Segera Disiapkan' : 'Terima Kasih');
              if (!noPrice) {
                  encoder.line('Atas Kunjungan Anda');
              }
              
              encoder.feed(3);
              encoder.cut();

              return encoder.getRaw();
          }

          // Helper: Generate structured 58mm HTML receipt inside dynamic iframe
          function printHtmlReceipt(data, noPrice = false) {
              const tx = data.transaction;
              const items = tx.order_item || [];
              const res = data.restaurant || {};
              const cashier = data.user || 'Kasir';
              
              let itemsHtml = '';
              items.forEach(elem => {
                  itemsHtml += `
                      <div style="margin-bottom: 8px;">
                          <p style="margin: 0; font-weight: bold; font-size: 13px;">${elem.product_name}</p>
                          ${elem.note ? `<p style="margin: 2px 0 2px 10px; font-style: italic; font-size: 11px;">* Note: ${elem.note}</p>` : ''}
                          <div style="display: flex; justify-content: space-between; font-size: 12px; margin-top: 2px;">
                              ${noPrice ? `
                              <span style="font-weight: bold; font-size: 13px;">Qty: ${elem.qty}</span>
                              ` : `
                              <span>${elem.qty} x Rp ${addCommas(elem.price || (elem.subtotal/elem.qty))}</span>
                              <span style="font-weight: bold;">Rp ${addCommas(elem.subtotal)}</span>
                              `}
                          </div>
                      </div>
                  `;
              });

              const tax = tx.tax || 0;
              const discount = tx.discount || 0;
              const total = tx.total;
              const paid = tx.total_paid || 0;
              const changed = paid > 0 ? (paid - total) : 0;

              const receiptHtml = `
                  <html>
                  <head>
                      <title>Print Receipt</title>
                      <style>
                          @page { margin: 0; }
                          body {
                              font-family: 'Courier New', Courier, monospace;
                              width: 58mm;
                              margin: 0;
                              padding: 10px;
                              box-sizing: border-box;
                              color: #000;
                              background: #fff;
                          }
                          .text-center { text-align: center; }
                          .text-right { text-align: right; }
                          .bold { font-weight: bold; }
                          .divider { border-top: 1px dashed #000; margin: 8px 0; }
                          .row { display: flex; justify-content: space-between; font-size: 12px; margin: 3px 0; }
                          h4 { margin: 0; font-size: 15px; font-weight: bold; }
                          p { margin: 2px 0; font-size: 11px; }
                      </style>
                  </head>
                  <body>
                      <div class="text-center" style="margin-bottom: 8px;">
                          ${!noPrice && res.logo ? `<img src="${res.logo}" alt="logo" style="max-height: 45px; max-width: 90px; object-fit: contain; margin-bottom: 6px; filter: grayscale(100%); display: inline-block;"><br>` : ''}
                          <h4 style="text-transform: uppercase; font-size: 14px; margin: 4px 0;">${noPrice ? 'KITCHEN CHECK' : (res.name || 'POS KASIR')}</h4>
                          ${!noPrice && res.location ? `<p style="font-size: 10px; margin: 2px 0 0 0; line-height: 1.2;">${res.location}</p>` : ''}
                      </div>
                      <div class="divider"></div>
                      <div>
                          <p>Tanggal: ${moment(tx.paid_at || tx.created_at).format('DD/MM/YYYY HH:mm')}</p>
                          <p>Invoice: #${tx.invoice_number}</p>
                          <p>Meja   : ${tx.order_type === 'take_away' ? 'Take Away' : (tx.table ? tx.table.name : 'Dine In')}</p>
                          <p>Kasir  : ${cashier}</p>
                      </div>
                      <div class="divider"></div>
                      <div>
                          ${itemsHtml}
                      </div>
                      <div class="divider"></div>
                      ${!noPrice ? `
                      <div>
                          <div class="row">
                              <span>Subtotal</span>
                              <span>Rp ${addCommas(tx.subtotal || total)}</span>
                          </div>
                          <div class="row">
                              <span>Pajak (10%)</span>
                              <span>Rp ${addCommas(tax)}</span>
                          </div>
                          ${discount > 0 ? `
                          <div class="row" style="color: red;">
                              <span>Diskon</span>
                              <span>-Rp ${addCommas(discount)}</span>
                          </div>
                          ` : ''}
                          <div class="divider"></div>
                          <div class="row bold" style="font-size: 13px;">
                              <span>TOTAL</span>
                              <span>Rp ${addCommas(total)}</span>
                          </div>
                          <div class="divider"></div>
                          <div class="row">
                              <span>Dibayar (${tx.paid_method || 'CASH'})</span>
                              <span>Rp ${addCommas(paid)}</span>
                          </div>
                          <div class="row">
                              <span>Kembalian</span>
                              <span>Rp ${addCommas(changed)}</span>
                          </div>
                      </div>
                      <div class="divider" style="border-top: 1px double #000;"></div>
                      ` : ''}
                      <div class="text-center" style="margin-top: 10px; font-size: 12px; font-weight: bold;">
                          <p>${noPrice ? 'Sajian Segera Disiapkan' : 'Terima Kasih'}</p>
                          ${!noPrice ? '<p>Atas Kunjungan Anda</p>' : ''}
                      </div>
                  </body>
                  </html>
              `;

              let iframe = document.getElementById('bt-print-iframe');
              if (!iframe) {
                  iframe = document.createElement('iframe');
                  iframe.id = 'bt-print-iframe';
                  iframe.style.position = 'absolute';
                  iframe.style.width = '0px';
                  iframe.style.height = '0px';
                  iframe.style.border = 'none';
                  iframe.style.left = '-9999px';
                  document.body.appendChild(iframe);
              }

              const doc = iframe.contentDocument || iframe.contentWindow.document;
              doc.open();
              doc.write(receiptHtml);
              doc.close();

              setTimeout(() => {
                  iframe.contentWindow.focus();
                  iframe.contentWindow.print();
              }, 250);
          }
    </script>
@endsection