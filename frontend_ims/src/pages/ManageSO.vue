<template>
    <div class="relative h-full w-full min-h-fit min-w-[200px]">
        
        <transition
            :enter-from-class="mainContentTransition.enterFrom "
            :enter-to-class="mainContentTransition.enterTo"
            :enter-active-class="mainContentTransition.enterActive"
            :leave-from-class="mainContentTransition.leaveFrom"
            :leave-to-class="mainContentTransition.leaveTo"
            :leave-active-class="mainContentTransition.leaveActive"
            @after-leave="changeContent(pageContent)"
        >
            <section v-show="viewingSalesList" class="h-full flex flex-col max-h-[1200px] min-h-[800px] lg:min-h-[600px] w-full max-w-[1300px] mx-auto">
                <div class="flex flex-col items-start px-2 mt-2 mb-4">
                    <h3 class="font-semibold tracking-wider">Manage Sales Orders</h3>
                </div>
                
                <div class="flex flex-1 p-2 overflow-hidden">
                    <div class="flex flex-col flex-1 overflow-hidden min-h-[350px] bg-white rounded-xl shadow-[0px_1px_4px_rgba(0,0,0,0.1)] p-6">
                        
                        <!-- Search bar -->
                        <div class="flex mb-4">
                            <input 
                                v-model="searchSaleValue" 
                                type="text" 
                                placeholder="Search by SO number or status" 
                                class="search-bar"
                            >
                        </div>

                        <div class="flex flex-col flex-1 pb-2 overflow-hidden">

                            <!-- Desktop table layout -->
                            <div class="relative flex-1 hidden overflow-hidden lg:flex">
                                
                                <!-- Gradient overlay -->
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                            
                                <div class="flex-1 px-1 overflow-auto">
                                    <div class="grid grid-cols-[25%_20%_25%_20%_10%] head-row">
                                        <div class="head-data">SO Number</div>
                                        <div class="head-data">Date</div>
                                        <div class="head-data">Remarks</div>
                                        <div class="head-data">Status</div>
                                        <div class="justify-center head-data">Action</div>
                                    </div>

                                    <div v-if="dataStatus" class="flex items-center justify-center w-full py-3">
                                        <p>{{ dataStatus }}</p>
                                    </div>

                                    <div v-if="!dataStatus" v-for="sale in filteredSalesListData" class="grid grid-cols-[25%_20%_25%_20%_10%] body-row">
                                        <div class="body-data so-number-style-wide">
                                            {{ sale.soNumber }}
                                        </div>
                                        <div class="body-data so-date-style-wide">
                                            {{ formatDate(sale.createdDate) }}
                                        </div>
                                        <div class="body-data">
                                            <div class="po-remarks-style-wide" :title="sale.remarks">
                                                {{ sale.remarks ?? 'no remarks' }}
                                            </div>
                                        </div>
                                        <div class="body-data">
                                            <div :class="['px-3 py-1 w-fit text-xs font-medium rounded text-nowrap h-fit uppercase', 
                                                sale.status == 'Pending' ? 'bg-yellow-100 text-yellow-600' : '', 
                                                sale.status == 'Paid' ? 'bg-blue-100 text-blue-600' : '',
                                                sale.status == 'Cancelled' ? 'bg-red-100 text-red-600' : '', 
                                                sale.status == 'Returned' ? 'bg-orange-100 text-orange-600' : ''
                                                ]"
                                            >
                                                {{ sale.status }}
                                            </div>
                                        </div>
                                        <div class="flex justify-center items-center body-data">
                                            <button 
                                                @click="toggleMoreActions(sale.hdrId)" 
                                                class="px-4 py-1 mx-auto rounded-md btnActions hover:bg-gray-300"
                                                type="button"
                                            >
                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                            </button>

                                            <Transition
                                                enter-from-class="translate-x-full opacity-0 "
                                                enter-to-class="translate-x-0 opacity-100"
                                                enter-active-class="transition duration-300 ease-out transform"
                                                leave-from-class="translate-x-0 opacity-100"
                                                leave-to-class="translate-x-full opacity-0"
                                                leave-active-class="transition duration-300 ease-in transform"
                                            >
                                                <div v-if="currentSaleId == sale.hdrId" class="absolute inset-0 flex items-center justify-end w-full h-full ">
                                                    <div class="bg-gradient-to-r flex-1 h-full from-[rgba(0,0,0,0.01)] to-darkgray-pri "></div>
                                                    <div class="flex items-center justify-end h-full p-2 bg-darkgray-pri w-fit btnActions">
                                                        <button 
                                                            @click="showSaleDetails(sale)"
                                                            class="h-full px-4 font-semibold text-white rounded-md w-fit hover:bg-darkgray-sec"
                                                            type="button"
                                                            title="View"
                                                        >
                                                            View
                                                        </button>

                                                        <button 
                                                            v-if="sale.status == 'Pending'"
                                                            @click="showPayForm(sale)"
                                                            class="h-full px-4 font-semibold text-white rounded-md w-fit hover:bg-darkgray-sec"
                                                            type="button"
                                                            title="Mark as Paid"
                                                        >
                                                            Mark as Paid
                                                        </button>

                                                        <button 
                                                            v-if="sale.status == 'Pending'"
                                                            @click="showUpdateForm(sale)"
                                                            class="h-full px-4 font-semibold text-white rounded-md w-fit hover:bg-darkgray-sec"
                                                            type="button"
                                                            title="Update"
                                                        >
                                                            Update
                                                        </button>

                                                        <button 
                                                            v-if="sale.status == 'Pending'"
                                                            @click="showCancelForm(sale)"
                                                            class="h-full px-4 font-semibold text-white rounded-md w-fit hover:bg-red-700 "
                                                            type="button"
                                                            title="Cancel"
                                                        >
                                                            Cancel
                                                        </button>

                                                        <button 
                                                            v-if="sale.status == 'Paid'"
                                                            @click="showReturnForm(sale)"
                                                            class="h-full px-4 font-semibold text-white rounded-md w-fit hover:bg-red-700 "
                                                            type="button"
                                                            title="Return"
                                                        >
                                                            Return
                                                        </button>
                                                    </div>
                                                </div>
                                            </Transition>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mobile card layout -->
                            <div class="relative flex flex-1 overflow-hidden lg:hidden">

                                <!-- Gradient overlays -->
                                <div class="absolute top-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-b from-white to-transparent"></div>
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                    
                                <div class="flex flex-1 overflow-auto">

                                    <div v-if="dataStatus" class="flex items-center justify-center w-full py-3">
                                        <p>{{ dataStatus }}</p>
                                    </div>

                                    <!-- Card grid -->
                                    <div class="my-2 content-start flex-1 gap-3 p-1 overflow-hidden min-h-fit grid grid-cols-[repeat(auto-fill,minmax(250px,1fr))]">
                                    
                                        <!-- Card items -->
                                        <div 
                                            v-if="!dataStatus" 
                                            v-for="sale in filteredSalesListData" 
                                            class="relative flex flex-col rounded-lg shadow-[0px_1px_4px_rgba(0,0,0,0.1)] max-h-full hover:shadow-[0_1px_4px_rgba(0,0,0,0.25)] transition-all"
                                        >
                                            
                                            <!-- Card head -->
                                            <div class="flex items-center justify-between pt-5 pb-3 mx-5 mb-2 border-b border-gray-200">
                                                <div class="flex items-center w-full ">
                                                    <h5 class="so-number-style-narrow">
                                                        {{ sale.soNumber }}
                                                    </h5>

                                                    <button 
                                                        @click="toggleMoreActions(sale.hdrId)" 
                                                        class="px-4 py-1 ml-auto rounded-md btnActions hover:bg-gray-300"
                                                        type="button"
                                                    >
                                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- Card body -->
                                            <div class="grid gap-2 px-5 pt-2 pb-5 cursor-default">

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <div class="label-style-two">Status:</div> 
                                                    <div 
                                                        :class="['px-3 py-1 w-fit text-xs font-medium rounded text-nowrap ml-auto', 
                                                            sale.status == 'Pending' ? 'bg-yellow-100 text-yellow-600' : '', 
                                                            sale.status == 'Paid' ? 'bg-blue-100 text-blue-600' : '',
                                                            sale.status == 'Cancelled' ? 'bg-red-100 text-red-600' : '', 
                                                            sale.status == 'Returned' ? 'bg-orange-100 text-orange-600' : ''
                                                        ]"
                                                    >
                                                        {{ sale.status }}
                                                    </div>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <div class="label-style-two">Date:</div> 
                                                    <div class="ml-auto so-date-style-narrow">
                                                        {{ formatDate(sale.createdDate) }}
                                                    </div>
                                                </div>

                                                <div class="so-remarks-style-narrow" :title="sale.remarks">
                                                    {{ sale.remarks ?? 'no remarks' }}
                                                </div>
                                            </div>

                                            <!-- More actions -->
                                            <Transition
                                                enter-from-class="opacity-0"
                                                enter-to-class="opacity-100 "
                                                enter-active-class="transition duration-300 ease-out"
                                                leave-from-class="opacity-100 "
                                                leave-to-class="opacity-0"
                                                leave-active-class="transition duration-200 ease-in"
                                            >
                                                <div v-if="currentSaleId == sale.hdrId" class="absolute flex flex-col items-center justify-center w-full h-full gap-1 rounded-lg bg-black/60 ">
                                                    <div class="grid w-3/5 grid-cols-1 gap-1 btnActions min-w-[150px]">
                                                        <button 
                                                            @click="showSaleDetails(sale)"
                                                            class="w-full px-4 py-2 bg-white rounded-lg hover:shadow-[0_0_6px] hover:shadow-white/70" 
                                                            type="button"
                                                            title="View"
                                                        >
                                                            View
                                                        </button>

                                                        <button 
                                                            v-if="sale.status == 'Pending'"
                                                            @click="showPayForm(sale)"
                                                            class="w-full px-4 py-2 bg-white rounded-lg hover:shadow-[0_0_6px] hover:shadow-white/70" 
                                                            type="button"
                                                            title="Mark as Paid"
                                                        >
                                                            Mark as Paid
                                                        </button>

                                                        <button 
                                                            v-if="sale.status == 'Pending'"
                                                            @click="showUpdateForm(sale)"
                                                            class="w-full px-4 py-2 bg-white rounded-lg hover:shadow-[0_0_6px] hover:shadow-white/70" 
                                                            type="button"
                                                            title="Update"
                                                        >
                                                            Update
                                                        </button>

                                                        <button 
                                                            v-if="sale.status == 'Pending'"
                                                            @click="showCancelForm(sale)"
                                                            class="w-full px-4 py-2 bg-white text-red-700 rounded-lg hover:shadow-[0_0_6px] hover:shadow-red-500/90" 
                                                            type="button"
                                                            title="Cancel"
                                                        >
                                                            Cancel
                                                        </button>

                                                        <button 
                                                            v-if="sale.status == 'Paid'"
                                                            @click="showReturnForm(sale)"
                                                            class="w-full px-4 py-2 bg-white text-red-700 rounded-lg hover:shadow-[0_0_6px] hover:shadow-red-500/90"
                                                            type="button"
                                                            title="Return"
                                                        >
                                                            Return
                                                        </button>
                                                    </div>
                                                </div>
                                            </Transition>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </transition>

        <!-- Update sale -->
        <transition
            :enter-from-class="formTransition.enterFrom"
            :enter-to-class="formTransition.enterTo"
            :enter-active-class="formTransition.enterActive"
            :leave-from-class="formTransition.leaveFrom"
            :leave-to-class="formTransition.leaveTo"
            :leave-active-class="formTransition.leaveActive"
            @after-leave="changeContent('Sales List')"
        >
            <div v-show="viewingUpdateSale" class="flex flex-col w-full max-h-[1500px] min-h-[1400px] lg:max-h-[1300px] lg:min-h-[1200px] h-full max-w-[1300px] mx-auto ">
                <form @submit.prevent="updateSale" class="flex flex-col flex-1 w-full p-2 mx-auto overflow-hidden">
                    <section class="shadow-[0px_1px_4px_rgba(0,0,0,0.1)] overflow-hidden flex-1 p-6 sm:p-8 bg-white rounded-xl flex flex-col mb-4">
                        <div class="flex flex-row items-start">
                            <div class="flex flex-col">
                                <h3 class="font-bold text-left px-1">Update Sales Order</h3>
                                <p class="mb-5 text-sm text-gray-500 px-1">Update sales order details</p>
                            </div>
                            
                            <p class="px-3 py-1 ml-auto font-medium text-yellow-600 bg-yellow-100 rounded w-fit text-nowrap">
                                {{ "Status: " + updateFormData.status }}
                            </p>
                        </div>

                        <hr class="mb-5 mx-1">

                        <div class="mb-5 flex items-center gap-2 text-lg">
                            <i class="fas fa-info-circle"></i>
                            <span class="mr-2 font-medium">Order Details</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-x-4">
                            <TextDisplay
                                label="SO Number:"
                                :value="updateFormData.soNumber"
                            />

                            <TextDisplay
                                label="Date Created:"
                                :value="formatDate(updateFormData.createdDate)"
                            />
                        </div>

                        <SelectDropdown
                            v-model="updateFormData.customerId"
                            label="Customer:"
                            :listData="customersListData"
                        />

                        <TextArea
                            v-model="updateFormData.remarks" 
                            id="remarks-update" 
                            label="Remarks:"
                        />

                        <TextDisplay
                            label="Created by:"
                            :value="updateFormData.createdByName"
                        />
                    
                        <hr class="mt-2 mb-5 mx-1">

                        <div class="flex flex-col flex-1 overflow-hidden">

                            <div class="mb-5 flex flex-row flex-wrap items-end justify-between gap-y-2">
                                <div class="flex items-center gap-2 text-lg">
                                    <i class="fas fa-list"></i>
                                    <span class="mr-2 font-medium">Selected Items</span>
                                </div>

                                <ButtonDark type="button" @click="showItemsListModal">
                                    <div class="relative flex flex-row items-center w-fit ">
                                        <span class="text-sm px-2">
                                            <i class="fa-solid fa-plus"></i>
                                        </span>
                                        <span class="pr-2">Add Item</span>
                                    </div>
                                </ButtonDark>
                            </div>

                            <!-- Desktop table layout -->
                            <div class="relative flex-1 hidden overflow-hidden lg:flex">
                                
                                <!-- Gradient overlay -->
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                                
                                <div class="flex-1 px-1 overflow-auto">
                                    <div class="grid grid-cols-[auto_20%_25%_20%_80px] head-row">
                                        <div class="head-data">Item</div>
                                        <div class="head-data">Price</div>
                                        <div class="head-data">Quantity</div>
                                        <div class="head-data">Subtotal</div>
                                        <div class="justify-center head-data">Action</div>
                                    </div>

                                    <div v-for="item in updateFormData.selectedItemsList" class="grid grid-cols-[auto_20%_25%_20%_80px] body-row">
                                        <div class="grid body-data">
                                            <p class="item-name-style-wide" :title="item.itemName">
                                                {{ item.itemName }}
                                            </p>
                                            <p class="item-sku-style-wide" :title="item.sku">
                                                {{ item.sku }}
                                            </p>
                                        </div>
                                        <div class="body-data item-price-style-wide">
                                            ₱{{ item.price }}
                                        </div>
                                        <div class="body-data">
                                            <div class="flex flex-row w-full border border-gray-300 rounded-lg">
                                                <input 
                                                    v-model="item.quantity"
                                                    type="number"
                                                    class="flex-1 min-w-0 px-3 py-1.5 text-right text-sm bg-transparent focus:outline-none"
                                                    placeholder="0"
                                                >
                                                <span class="grid px-3 text-gray-500 border-l border-gray-300 whitespace-nowrap place-items-center">
                                                    {{ item.unit ?? (item.quantity < 2 ? 'unit' : 'units') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="body-data item-subtotal-style-wide">
                                            ₱{{ (item.price * item.quantity).toFixed(2)  }}
                                        </div>
                                        <div class="justify-center body-data">
                                            <button 
                                                @click="toggleSelectItem(item.dtlId, item.itemId)" 
                                                class="flex items-center justify-center text-white bg-red-500 rounded-full size-12"
                                                type="button" 
                                            >
                                                <i class="rotate-45 fa-solid fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>                            
                                </div>
                            </div>

                            <!-- Mobile card layout -->
                            <div class="relative flex flex-1 overflow-hidden lg:hidden">
                                
                                <!-- Gradient overlays -->
                                <div class="absolute top-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-b from-white to-transparent"></div>
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                    
                                <div class="flex flex-1 overflow-auto">

                                    <!-- Card grid -->
                                    <div class="my-2 flex-1 gap-3 overflow-hidden min-h-fit content-start grid grid-cols-[repeat(auto-fill,minmax(250px,1fr))] p-1">
                                    
                                        <!-- Card items -->
                                        <div v-for="item in updateFormData.selectedItemsList" class="cursor-default flex flex-col shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-lg max-h-full hover:shadow-[0_1px_4px_rgba(0,0,0,0.25)] transition-all">
                                        
                                            <!-- Card head -->
                                            <div class="flex items-center justify-between pt-5 pb-3 mx-5 mb-2 border-b border-gray-200">
                                                <div class="overflow-hidden">
                                                    <h5 class="item-name-style-narrow" :title="item.itemName">
                                                        {{ item.itemName }}
                                                    </h5>
                                                    <p class="item-sku-style-narrow" :title="item.sku">
                                                        {{ item.sku ?? 'Not set' }}
                                                    </p>
                                                </div>

                                                <div class="justify-center ml-auto w-fit">
                                                    <button 
                                                        @click="toggleSelectItem(item.dtlId, item.itemId)" 
                                                        class="flex items-center justify-center text-white bg-red-500 rounded-full size-12"
                                                        type="button"
                                                    >
                                                        <i class="rotate-45 fa-solid fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Card body -->
                                            <div class="grid gap-2 px-5 pt-2 pb-5">

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="label-style-two">Price</p>
                                                    <p class="ml-auto item-price-style-narrow">
                                                        ₱{{ item.price }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="w-1/2 label-style-two">Quantity</p>
                                                    <div class="flex items-center justify-between w-1/2 transition bg-white border border-gray-300 rounded-md">
                                                        <input 
                                                            v-model="item.quantity"
                                                            type="number"
                                                            class="flex-1 min-w-0 px-1 py-1.5 text-right text-sm bg-transparent"
                                                            placeholder="0"
                                                        >
                                                        <span class="px-3 text-sm text-gray-500 border-l border-gray-200 whitespace-nowrap">
                                                            {{ item.unit ?? (item.quantity < 2 ? 'unit' : 'units') }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center pt-2 border-t">
                                                    <p class="label-style-two">Subtotal</p>
                                                    <p class="ml-auto item-subtotal-style-narrow">
                                                        ₱{{ (item.price * item.quantity).toFixed(2)  }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </section>

                    <div class="flex items-end justify-end w-full">
                        <div class="grid grid-cols-2 w-full md:w-[400px] gap-2 lg:gap-4">
                            <ButtonYellow width="full" type="submit">
                                Save
                            </ButtonYellow>

                            <ButtonWhite @click="showSalesList" width="full" type="button">
                                Cancel
                            </ButtonWhite>
                        </div>
                    </div>
                </form>
            </div>
        </transition>

        <!-- Payment -->
        <transition
            :enter-from-class="formTransition.enterFrom"
            :enter-to-class="formTransition.enterTo"
            :enter-active-class="formTransition.enterActive"
            :leave-from-class="formTransition.leaveFrom"
            :leave-to-class="formTransition.leaveTo"
            :leave-active-class="formTransition.leaveActive"
            @after-leave="changeContent('Sales List')"
        >
            <div v-show="viewingPaySale" class="flex flex-col w-full max-h-[1500px] min-h-[1500px] lg:max-h-[1300px] lg:min-h-[1200px] h-full max-w-[1300px] mx-auto">
                <form @submit.prevent="paySale" class="flex flex-col flex-1 w-full p-2 mx-auto overflow-hidden">
                    <section class="shadow-[0px_1px_4px_rgba(0,0,0,0.1)] overflow-hidden flex-1 p-6 sm:p-8 bg-white rounded-xl flex flex-col mb-4">
                        <div class="flex flex-row items-start">
                            <div class="flex flex-col">
                                <h3 class="font-bold text-left px-1">Confirm Payment</h3>
                                <p class="mb-5 text-sm text-gray-500 px-1">Confirm payment for the sales order</p>
                            </div>
                            
                            <p class="px-3 py-1 ml-auto font-medium text-yellow-600 bg-yellow-100 rounded w-fit text-nowrap">
                                {{ "Status: " + payFormData.status }}
                            </p>
                        </div>

                        <hr class="mb-5 mx-1">

                        <div class="mb-5 flex items-center gap-2 text-lg">
                            <i class="fas fa-info-circle"></i>
                            <span class="mr-2 font-medium">Order Details</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-x-4">
                            <TextDisplay
                                label="SO Number:"
                                :value="payFormData.soNumber"
                            />
                            <TextDisplay
                                label="Date Created:"
                                :value="formatDate(payFormData.createdDate)"
                            />
                        </div>

                        <TextDisplay
                            label="Customer:"
                            :value="payFormData.customerName"
                        />

                        <TextArea
                            v-model="payFormData.remarks" 
                            id="remarks-payment" 
                            label="Remarks:"
                        />

                        <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-x-4">
                            <TextDisplay
                                label="Created by:"
                                :value="payFormData.createdByName"
                            />

                            <TextDisplay
                                label="Confirmed by:"
                                :value="payFormData.userName"
                            />
                        </div>

                        <hr class="mt-2 mb-5 mx-1">
                        
                        <div class="flex flex-col flex-1 overflow-hidden">
                            <div class="mb-5 flex items-center gap-2 text-lg">
                                <i class="fas fa-list"></i>
                                <span class="mr-2 font-medium">Selected Items</span>
                            </div>

                            <!-- Desktop table layout -->
                            <div class="relative flex-1 hidden overflow-hidden lg:flex">
                                
                                <!-- Gradient overlay -->
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                                
                                <div class="flex-1 px-1 overflow-auto">
                                    <div class="grid grid-cols-[40%_20%_20%_20%] head-row">
                                        <div class="head-data">Item</div>
                                        <div class="head-data">Price</div>
                                        <div class="head-data">Quantity</div>
                                        <div class="head-data">Subtotal</div>
                                    </div>

                                    <div v-for="item in payFormData.selectedItemsList" class="grid grid-cols-[40%_20%_20%_20%] body-row">
                                        <div class="grid body-data">
                                            <p class="item-name-style-wide" :title="item.itemName">
                                                {{ item.itemName }}
                                            </p>
                                            <p class="item-sku-style-wide" :title="item.sku">
                                                {{ item.sku }}
                                            </p>
                                        </div>
                                        <div class="body-data item-price-style-wide">
                                            ₱{{ item.price }}
                                        </div>
                                        <div class="body-data">
                                            <div class="text-gray-500 bg-gray-100 rounded-md h-fit">
                                                <p class="grid px-3  py-1.5 place-items-center">
                                                    {{ item.quantity + " " + (item.unit ?? (item.quantity < 2 ? 'unit' : 'units')) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="body-data item-subtotal-style-wide">
                                            ₱{{ (item.price * item.quantity).toFixed(2)  }}
                                        </div>
                                    </div>                            
                                </div>
                            </div>  
                            
                            <!-- Mobile card layout -->
                            <div class="relative flex flex-1 overflow-hidden lg:hidden">
                                
                                <!-- Gradient overlays -->
                                <div class="absolute top-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-b from-white to-transparent"></div>
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                    
                                <div class="flex flex-1 overflow-auto">

                                    <!-- Card grid -->
                                    <div class="my-2 content-start flex-1 gap-3 p-1 overflow-hidden min-h-fit grid grid-cols-[repeat(auto-fill,minmax(250px,1fr))]">
                                    
                                        <!-- Card items -->
                                        <div v-for="item in payFormData.selectedItemsList" class="cursor-default flex flex-col shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-lg max-h-full hover:shadow-[0_1px_4px_rgba(0,0,0,0.25)] transition-all">
                                        
                                            <!-- Card head -->
                                            <div class="flex items-center justify-between pt-5 pb-3 mx-5 mb-2 border-b border-gray-200">
                                                <div class="overflow-hidden">
                                                    <h5 class="item-name-style-narrow" :title="item.itemName">
                                                        {{ item.itemName }}
                                                    </h5>
                                                    <p class="item-sku-style-narrow" :title="item.sku">
                                                        {{ item.sku ?? 'Not set' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Card body -->
                                            <div class="grid gap-2 px-5 pt-2 pb-5">

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="label-style-two">Price</p>
                                                    <p class="ml-auto item-price-style-narrow">
                                                        ₱{{ item.price }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="w-1/2 label-style-two">Quantity</p>
                                                    <p class="w-1/2 ml-auto text-sm px-3 py-1.5  text-gray-500 text-right bg-gray-100 rounded-md">
                                                        {{ item.quantity + " " + (item.unit ?? (item.quantity < 2 ? 'unit' : 'units')) }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center pt-2 border-t">
                                                    <p class="label-style-two">Subtotal</p>
                                                    <p class="ml-auto item-subtotal-style-narrow">
                                                        ₱{{ (item.price * item.quantity).toFixed(2)  }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </section>

                    <div class="flex items-end justify-end w-full">
                        <div class="grid grid-cols-2 w-full md:w-[400px] gap-2 lg:gap-4">
                            <ButtonYellow width="full" type="submit">
                                Confirm payment
                            </ButtonYellow>

                            <ButtonWhite @click="showSalesList" width="full" type="button">
                                Cancel
                            </ButtonWhite>
                        </div>
                    </div>
                </form>
            </div>
        </transition>

        <!-- Cancel sale -->
        <transition
            :enter-from-class="formTransition.enterFrom"
            :enter-to-class="formTransition.enterTo"
            :enter-active-class="formTransition.enterActive"
            :leave-from-class="formTransition.leaveFrom"
            :leave-to-class="formTransition.leaveTo"
            :leave-active-class="formTransition.leaveActive"
            @after-leave="changeContent('Sales List')"
        >
            <div v-show="viewingCancelSale" class="flex flex-col w-full max-h-[1500px] min-h-[1400px] lg:max-h-[1300px] lg:min-h-[1200px] h-full max-w-[1300px] mx-auto ">
                <form @submit.prevent="cancelSale" class="flex flex-col flex-1 w-full p-2 mx-auto overflow-hidden">
                    <section class="shadow-[0px_1px_4px_rgba(0,0,0,0.1)] overflow-hidden flex-1 p-6 sm:p-8 bg-white rounded-xl flex flex-col mb-4">
                        <div class="flex flex-row items-start">
                            <div class="flex flex-col">
                                <h3 class="font-bold text-left px-1">Cancel Sales Order</h3>
                                <p class="mb-5 text-sm text-gray-500 px-1">Confirm cancellation of the sales order</p>
                            </div>
                            
                            <p class="px-3 py-1 ml-auto font-medium text-yellow-600 bg-yellow-100 rounded w-fit text-nowrap">
                                {{ "Status: " + cancelFormData.status }}
                            </p>
                        </div>

                        <hr class="mb-5 mx-1">

                        <div class="mb-5 flex items-center gap-2 text-lg">
                            <i class="fas fa-info-circle"></i>
                            <span class="mr-2 font-medium">Order Details</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-x-4">
                            <TextDisplay
                                label="SO Number:"
                                :value="cancelFormData.soNumber"
                            />
                            <TextDisplay
                                label="Date Created:"
                                :value="formatDate(cancelFormData.createdDate)"
                            />
                        </div>

                        <TextDisplay
                            label="Customer:"
                            :value="cancelFormData.customerName"
                        />

                        <TextArea
                            v-model="cancelFormData.remarks" 
                            id="remarks-cancel" 
                            label="Remarks:"
                        />

                        <TextDisplay
                            label="Created by:"
                            :value="cancelFormData.createdByName"
                        />

                        <hr class="mt-2 mb-5 mx-1">

                        <div class="flex flex-col flex-1 overflow-hidden">
                            <div class="mb-5 flex items-center gap-2 text-lg">
                                <i class="fas fa-list"></i>
                                <span class="mr-2 font-medium">Selected Items</span>
                            </div>

                            <!-- Desktop table layout -->
                            <div class="relative flex-1 hidden overflow-hidden lg:flex">
                                
                                <!-- Gradient overlay -->
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                                
                                <div class="flex-1 px-1 overflow-auto">
                                    <div class="grid grid-cols-[40%_20%_20%_20%] head-row">
                                        <div class="head-data">Item</div>
                                        <div class="head-data">Price</div>
                                        <div class="head-data">Quantity</div>
                                        <div class="head-data">Subtotal</div>
                                    </div>

                                    <div v-for="item in cancelFormData.selectedItemsList" class="grid grid-cols-[40%_20%_20%_20%] body-row">
                                        <div class="grid body-data">
                                            <p class="item-name-style-wide" :title="item.itemName">
                                                {{ item.itemName }}
                                            </p>
                                            <p class="item-sku-style-wide" :title="item.sku">
                                                {{ item.sku }}
                                            </p>
                                        </div>
                                        <div class="body-data item-price-style-wide">
                                            ₱{{ item.price }}
                                        </div>
                                        <div class="body-data">
                                            <div class="flex flex-row text-gray-500 bg-gray-100 rounded-md h-fit">
                                                <p class="grid px-3  py-1.5 place-items-center">
                                                    {{ item.quantity + " " + (item.unit ?? (item.quantity < 2 ? 'unit' : 'units')) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="body-data item-subtotal-style-wide">
                                            ₱{{ (item.price * item.quantity).toFixed(2)  }}
                                        </div>
                                    </div>                            
                                </div>
                            </div>  
                            
                            <!-- Mobile card layout -->
                            <div class="relative flex flex-1 overflow-hidden lg:hidden">
                                
                                <!-- Gradient overlays -->
                                <div class="absolute top-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-b from-white to-transparent"></div>
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                    
                                <div class="flex flex-1 overflow-auto">

                                    <!-- Card grid -->
                                    <div class="my-2 content-start flex-1 gap-3 p-1 overflow-hidden min-h-fit grid grid-cols-[repeat(auto-fill,minmax(250px,1fr))]">
                                    
                                        <!-- Card items -->
                                        <div v-for="item in cancelFormData.selectedItemsList" class="cursor-default flex flex-col shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-lg max-h-full hover:shadow-[0_1px_4px_rgba(0,0,0,0.25)] transition-all">
                                        
                                            <!-- Card head -->
                                            <div class="flex items-center justify-between pt-5 pb-3 mx-5 mb-2 border-b border-gray-200">
                                                <div class="overflow-hidden">
                                                    <h5 class="item-name-style-narrow" :title="item.itemName">
                                                        {{ item.itemName }}
                                                    </h5>
                                                    <p class="item-sku-style-narrow" :title="item.sku">
                                                        {{ item.sku ?? 'Not set' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Card body -->
                                            <div class="grid gap-2 px-5 pt-2 pb-5 ">
                                                
                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="label-style-two">Price</p>
                                                    <p class="ml-auto item-price-style-narrow">
                                                        ₱{{ item.price }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="w-1/2 label-style-two">Quantity</p>
                                                    <p class="w-1/2 ml-auto text-sm px-3 py-1.5  text-gray-500 text-right bg-gray-100 rounded-md">
                                                        {{ item.quantity + " " + (item.unit ?? (item.quantity < 2 ? 'unit' : 'units')) }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center pt-2 border-t">
                                                    <p class="label-style-two">Subtotal</p>
                                                    <p class="ml-auto item-subtotal-style-narrow">
                                                        ₱{{ (item.price * item.quantity).toFixed(2)  }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </section>

                    <div class="flex items-end justify-end w-full">
                        <div class="grid grid-cols-2 w-full md:w-[400px] gap-2 lg:gap-4">
                            <ButtonRed width="full" type="submit">
                                Cancel SO
                            </ButtonRed>

                            <ButtonWhite @click="showSalesList" width="full" type="button">
                                Keep SO
                            </ButtonWhite>
                        </div>
                    </div>
                </form>
            </div>
        </transition>

        <!-- Return sale -->
        <transition
            :enter-from-class="formTransition.enterFrom"
            :enter-to-class="formTransition.enterTo"
            :enter-active-class="formTransition.enterActive"
            :leave-from-class="formTransition.leaveFrom"
            :leave-to-class="formTransition.leaveTo"
            :leave-active-class="formTransition.leaveActive"
            @after-leave="changeContent('Sales List')"
        >
            <div v-show="viewingReturnSale" class="flex flex-col w-full max-h-[1600px] min-h-[1600px] lg:max-h-[1300px] lg:min-h-[1200px] h-full max-w-[1300px] mx-auto ">
                <form @submit.prevent="returnSale" class="flex flex-col flex-1 w-full p-2 mx-auto overflow-hidden">
                    <section class="shadow-[0px_1px_4px_rgba(0,0,0,0.1)] overflow-hidden flex-1 p-6 sm:p-8 bg-white rounded-xl flex flex-col mb-4">

                        <div class="flex flex-row items-start">
                            <div class="flex flex-col">
                                <h3 class="font-bold text-left px-1">Return Sales Order</h3>
                                <p class="mb-5 text-sm text-gray-500 px-1">Confirm return of the sales order</p>
                            </div>
                            
                            <p class="px-3 py-1 ml-auto font-medium text-blue-600 bg-blue-100 rounded w-fit text-nowrap">
                                {{ "Status: " + returnFormData.status }}
                            </p>
                        </div>

                        <hr class="mb-5 mx-1">

                        <div class="mb-5 flex items-center gap-2 text-lg">
                            <i class="fas fa-info-circle"></i>
                            <span class="mr-2 font-medium">Order Details</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-x-4">
                            <TextDisplay
                                label="SO Number:"
                                :value="returnFormData.soNumber"
                            />
                            <TextDisplay
                                label="Customer:"
                                :value="returnFormData.customerName"
                            />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-x-4">
                            <TextDisplay
                                label="Date Created:"
                                :value="formatDate(returnFormData.createdDate)"
                            />
                            <TextDisplay
                                label="Date Confirmed:"
                                :value="formatDate(returnFormData.confirmedDate)"
                            />
                        </div>

                        <TextArea
                            v-model="returnFormData.remarks" 
                            id="remarks-return" 
                            label="Remarks:"
                        />

                        <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-x-4">
                            <TextDisplay
                                label="Created by:"
                                :value="returnFormData.createdByName"
                            />

                            <TextDisplay
                                label="Confirmed by:"
                                :value="returnFormData.confirmedByName"
                            />
                        </div>
                        
                        <hr class="mt-2 mb-5 mx-1">
                        
                        <div class="flex flex-col flex-1 overflow-hidden">
                            <div class="mb-5 flex items-center gap-2 text-lg">
                                <i class="fas fa-list"></i>
                                <span class="mr-2 font-medium">Selected Items</span>
                            </div>
                        
                            <!-- Desktop table layout -->
                            <div class="relative flex-1 hidden overflow-hidden lg:flex">
                                
                                <!-- Gradient overlay -->
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
    
                                <div class="flex-1 px-1 overflow-auto">
                                    <div class="grid grid-cols-[40%_20%_20%_20%] head-row">
                                        <div class="head-data">Item</div>
                                        <div class="head-data">Price</div>
                                        <div class="head-data">Quantity</div>
                                        <div class="head-data">Subtotal</div>
                                    </div>

                                    <div v-for="item in returnFormData.selectedItemsList" class="grid grid-cols-[40%_20%_20%_20%] body-row">
                                        <div class="grid body-data">
                                            <p class="item-name-style-wide" :title="item.itemName">
                                                {{ item.itemName }}
                                            </p>
                                            <p class="item-sku-style-wide" :title="item.sku">
                                                {{ item.sku }}
                                            </p>
                                        </div>
                                        <div class="body-data item-price-style-wide">
                                            ₱{{ item.price }}
                                        </div>
                                        <div class="body-data">
                                            <div class="text-gray-500 bg-gray-100 rounded-md h-fit">
                                                <p class="grid px-3  py-1.5 place-items-center">
                                                    {{ item.quantity + " " + (item.unit ?? (item.quantity < 2 ? 'unit' : 'units')) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="body-data item-subtotal-style-wide">
                                            ₱{{ (item.price * item.quantity).toFixed(2)  }}
                                        </div>
                                    </div>                            
                                </div>
                            </div>
                            
                            <!-- Mobile card layout -->
                            <div class="relative flex flex-1 overflow-hidden lg:hidden">
                                
                                <!-- Gradient overlays -->
                                <div class="absolute top-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-b from-white to-transparent"></div>
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                    
                                <div class="flex flex-1 overflow-auto">
                                    
                                    <!-- Card grid -->
                                    <div class="my-2 content-start flex-1 gap-3 p-1 overflow-hidden min-h-fit grid grid-cols-[repeat(auto-fill,minmax(250px,1fr))]">
                                    
                                        <!-- Card items -->
                                        <div v-for="item in returnFormData.selectedItemsList" class="cursor-default flex flex-col shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-lg max-h-full hover:shadow-[0_1px_4px_rgba(0,0,0,0.25)] transition-all">
                                        
                                            <!-- Card head -->
                                            <div class="flex items-center justify-between pt-5 pb-3 mx-5 mb-2 border-b border-gray-200">
                                                <div class="overflow-hidden">
                                                    <h5 class="item-name-style-narrow" :title="item.itemName">
                                                        {{ item.itemName }}
                                                    </h5>
                                                    <p class="item-sku-style-narrow" :title="item.sku">
                                                        {{ item.sku ?? 'Not set' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Card body -->
                                            <div class="grid gap-2 px-5 pt-2 pb-5">

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="label-style-two">Price</p>
                                                    <p class="ml-auto item-price-style-narrow">
                                                        ₱{{ item.price }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="w-1/2 label-style-two">Quantity</p>
                                                    <p class="w-1/2 ml-auto px-3 py-1.5 text-sm text-gray-500 text-right bg-gray-100 rounded-md">
                                                        {{ item.quantity + " " + (item.unit ?? (item.quantity < 2 ? 'unit' : 'units')) }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center pt-2 border-t">
                                                    <p class="label-style-two">Subtotal</p>
                                                    <p class="ml-auto item-subtotal-style-narrow">
                                                        ₱{{ (item.price * item.quantity).toFixed(2) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </section>

                    <div class="flex items-end justify-end w-full">
                        <div class="grid grid-cols-2 w-full md:w-[400px] gap-2 lg:gap-4">
                            <ButtonRed width="full" type="submit">
                                Confirm return
                            </ButtonRed>

                            <ButtonWhite @click="showSalesList" width="full" type="button">
                                Cancel
                            </ButtonWhite>
                        </div>
                    </div>
                </form>
            </div>
        </transition>

        <!-- View only -->
        <transition
            :enter-from-class="formTransition.enterFrom"
            :enter-to-class="formTransition.enterTo"
            :enter-active-class="formTransition.enterActive"
            :leave-from-class="formTransition.leaveFrom"
            :leave-to-class="formTransition.leaveTo"
            :leave-active-class="formTransition.leaveActive"
            @after-leave="changeContent('Sales List')"
        >
            <div v-show="viewingSaleDetails" class="flex flex-col w-full max-h-[1600px] min-h-[1600px] lg:max-h-[1300px] lg:min-h-[1200px] h-full max-w-[1300px] mx-auto ">
                <div class="flex flex-col flex-1 w-full p-2 mx-auto overflow-hidden">
                    <section class="shadow-[0px_1px_4px_rgba(0,0,0,0.1)] overflow-hidden flex-1 p-6 sm:p-8 bg-white rounded-xl flex flex-col mb-4">
                        <div class="flex flex-row items-start">
                            <div class="flex flex-col">
                                <h3 class="font-bold text-left px-1">Sales Order Details</h3>
                                <p class="mb-5 text-sm text-gray-500 px-1">View sales order details</p>
                            </div>
                            
                            <p 
                                :class="['px-3 py-1 w-fit font-medium rounded text-nowrap ml-auto', 
                                    saleDetails.status == 'Pending' ? 'bg-yellow-100 text-yellow-600' : '', 
                                    saleDetails.status == 'Paid' ? 'bg-blue-100 text-blue-600' : '',
                                    saleDetails.status == 'Cancelled' ? 'bg-red-100 text-red-600' : '', 
                                    saleDetails.status == 'Returned' ? 'bg-orange-100 text-orange-600' : ''
                                ]"
                            >
                                {{ "Status: " + saleDetails.status }}
                            </p>
                        </div>

                        <hr class="mb-5 mx-1">

                        <div class="mb-5 flex items-center gap-2 text-lg">
                            <i class="fas fa-info-circle"></i>
                            <span class="mr-2 font-medium">Order Details</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-x-4">
                            <TextDisplay
                                label="SO Number:"
                                :value="saleDetails.soNumber"
                            />
                            <TextDisplay
                                label="Customer:"
                                :value="saleDetails.customerName"
                            />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-x-4">
                            <TextDisplay
                                label="Date Created:"
                                :value="formatDate(saleDetails.createdDate)"
                            />
                            <TextDisplay
                                label="Confirmed Date:"
                                :value="saleDetails.confirmedDate ? formatDate(saleDetails.confirmedDate) : null"
                            />
                        </div>

                        <TextArea
                            v-model="saleDetails.remarks" 
                            id="remarks-view" 
                            label="Remarks:"
                        />

                        <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-x-4">
                            <TextDisplay
                                label="Created by:"
                                :value="saleDetails.createdByName"
                            />

                            <TextDisplay
                                label="Confirmed by:"
                                :value="saleDetails.confirmedByName"
                            />
                        </div>

                        <hr class="mt-2 mb-5 mx-1">
                        
                        <div class="flex flex-col flex-1 overflow-hidden">
                            <div class="mb-5 flex items-center gap-2 text-lg">
                                <i class="fas fa-list"></i>
                                <span class="mr-2 font-medium">Selected Items</span>
                            </div>

                            <!-- Desktop table layout -->
                            <div class="relative flex-1 hidden overflow-hidden lg:flex">
                                
                                <!-- Gradient overlay -->
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                                
                                <div class="flex-1 px-1 overflow-auto">
                                    <div class="grid grid-cols-[40%_20%_20%_20%] head-row">
                                        <div class="head-data">Item</div>
                                        <div class="head-data">Price</div>
                                        <div class="head-data">Quantity</div>
                                        <div class="head-data">Subtotal</div>
                                    </div>

                                    <div v-for="item in saleDetails.selectedItemsList" class="grid grid-cols-[40%_20%_20%_20%] body-row">
                                        <div class="grid body-data">
                                            <p class="item-name-style-wide" :title="item.itemName">
                                                {{ item.itemName }}
                                            </p>
                                            <p class="item-sku-style-wide" :title="item.sku">
                                                {{ item.sku }}
                                            </p>
                                        </div>
                                        <div class="body-data item-price-style-wide">
                                            ₱{{ item.price }}
                                        </div>
                                        <div class="body-data">
                                            <div class="text-gray-500 bg-gray-100 rounded-md h-fit">
                                                <p class="grid px-3  py-1.5 place-items-center">
                                                    {{ item.quantity + " " + (item.unit ?? (item.quantity < 2 ? 'unit' : 'units')) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="body-data item-subtotal-style-wide">
                                            ₱{{ (item.price * item.quantity).toFixed(2)  }}
                                        </div>
                                    </div>                            
                                </div>
                            </div>

                            <!-- Mobile card layout -->
                            <div class="relative flex flex-1 overflow-hidden lg:hidden">
                                
                                <!-- Gradient overlays -->
                                <div class="absolute top-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-b from-white to-transparent"></div>
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                    
                                <div class="flex flex-1 overflow-auto">

                                    <!-- Card grid -->
                                    <div class="my-2 content-start flex-1 gap-3 p-1 overflow-hidden min-h-fit grid grid-cols-[repeat(auto-fill,minmax(250px,1fr))]">
                                    
                                        <!-- Card items -->
                                        <div v-for="item in saleDetails.selectedItemsList" class="cursor-default flex flex-col shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-lg max-h-full hover:shadow-[0_1px_4px_rgba(0,0,0,0.25)] transition-all">
                                        
                                            <!-- Card head -->
                                            <div class="flex items-center  justify-between pt-5 pb-3 mx-5 mb-2 border-b border-gray-200">
                                                <div class="overflow-hidden">
                                                    <h5 class="item-name-style-narrow" :title="item.itemName">
                                                        {{ item.itemName }}
                                                    </h5>
                                                    <p class="item-sku-style-narrow" :title="item.sku">
                                                        {{ item.sku ?? 'Not set' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Card body -->
                                            <div class="grid gap-2 px-5 pt-2 pb-5">

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="label-style-two">Price</p>
                                                    <p class="ml-auto item-price-style-narrow">
                                                        ₱{{ item.price }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="w-1/2 label-style-two">Quantity</p>
                                                    <p class="w-1/2 ml-auto px-3 py-1.5 text-sm text-gray-500 text-right bg-gray-100 rounded-md">
                                                        {{ item.quantity + " " + (item.unit ?? (item.quantity < 2 ? 'unit' : 'units')) }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center pt-2 border-t">
                                                    <p class="label-style-two">Subtotal</p>
                                                    <p class="ml-auto item-subtotal-style-narrow">
                                                        ₱{{ (item.price * item.quantity).toFixed(2)  }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>  
                        
                    </section>

                    <div class="flex items-end justify-end w-full">
                        <div class="flex flex-row justify-between w-full sm:w-[200px] ">
                            <ButtonWhite @click="showSalesList" width="full" type="button">
                                Back
                            </ButtonWhite>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>

    <Modal :inUse="viewingItemsListModal" @close="viewingItemsListModal = false">
        <div v-show="viewingItemsListModal" class="w-full h-full mb-4 bg-white shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-xl max-h-[500px] max-w-[1000px]">
            <div class="flex flex-col size-full">
                
                <div class="flex flex-row flex-wrap gap-2 items-center px-6 py-2 text-warmyellow-pri bg-darkgray-pri rounded-t-xl border-b">
                    <h5 class="font-semibold tracking-wide">Add More Items</h5>

                    <button 
                        @click="viewingItemsListModal = false" 
                        class="ml-auto hover:bg-darkgray-sec rounded-full size-10 flex items-center justify-center cursor-pointer" 
                        title="close"
                        type="button"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="flex flex-col flex-1 overflow-hidden px-5 py-6">
                    
                    <!-- Search bar -->
                    <div class="flex flex-col items-end justify-between px-1 mb-2 space-y-2 sm:space-y-0 sm:flex-row">
                        <input 
                            v-model="searchItemValue" 
                            type="text" 
                            placeholder="Search by item name or SKU" 
                            class="search-bar"
                        >
                    </div>

                    <div class="flex flex-col flex-1 overflow-hidden">
                        
                        <!-- Desktop table layout -->
                        <div class="relative flex-1 hidden overflow-hidden lg:flex">
                            
                            <!-- Gradient overlay -->
                            <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                        
                            <div class="flex-1 px-1 overflow-auto">
                                <div class="grid grid-cols-[auto_25%_15%_15%_80px] head-row">
                                    <div class="head-data">Item</div>
                                    <div class="head-data">Category</div>
                                    <div class="head-data">Price</div>
                                    <div class="head-data">Quantity</div>
                                    <div class="justify-center head-data">Action</div>
                                </div>

                                <div v-if="itemsListDataStatus" class="flex items-center justify-center w-full py-3">
                                    <p>{{ itemsListDataStatus }}</p>
                                </div>

                                <div v-if="!itemsListDataStatus" v-for="item in filteredItemsListData" class="grid grid-cols-[auto_25%_15%_15%_80px] body-row">
                                    <div class="grid body-data">
                                        <p class="item-name-style-wide" :title="item.itemName">
                                            {{ item.itemName }}
                                        </p>
                                        <p class="item-sku-style-wide" :title="item.sku">
                                            {{ item.sku }}
                                        </p>
                                    </div>
                                    <div class="body-data item-category-style-wide" :title="item.category">
                                        {{ item.category }}
                                    </div>
                                    <div class="body-data item-price-style-wide">
                                        {{ item.price }}
                                    </div>
                                    <div class="body-data">
                                        <div class="item-quantity-style-wide"
                                            :class="getStockColor(item)"
                                        >
                                            {{ item.quantity + ' ' + (item.unit ?? ( item.quantity < 2 ? 'unit' : 'units') ) }}
                                        </div>
                                    </div>
                                    <div class="justify-center body-data">
                                        <button 
                                            @click="toggleSelectItem(null, item.id, item.itemName, item.price, item.unit, item.sku, item.category)" 
                                            :class=" selectedItemIds.includes(item.id) ? 'bg-red-500 text-white' : 'border-2 border-[#45a049] text-[#45a049] bg-[#eef3ee]' " 
                                            class="flex items-center justify-center rounded-full size-12"
                                            type="button"
                                        >
                                            <i :class="{'rotate-45': selectedItemIds.includes(item.id)}" class="transition duration-500 transform fa-solid fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile card layout -->
                        <div class="relative flex flex-1 overflow-hidden lg:hidden">
                            
                            <!-- Gradient overlays -->
                            <div class="absolute top-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-b from-white to-transparent"></div>
                            <div class="absolute bottom-0 left-0 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                
                            <div class="flex flex-1 overflow-auto">
                                
                                <div v-if="itemsListDataStatus" class="flex items-center justify-center w-full py-3">
                                    <p>{{ itemsListDataStatus }}</p>
                                </div>

                                <!-- Card grid -->
                                <div class="my-2 flex-1 gap-3 overflow-hidden min-h-fit content-start grid grid-cols-[repeat(auto-fill,minmax(250px,1fr))] p-1">
                                
                                    <!-- Card items -->
                                    <div v-if="!itemsListDataStatus" v-for="item in filteredItemsListData" class="cursor-default flex flex-col shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-lg max-h-full hover:shadow-[0_1px_4px_rgba(0,0,0,0.25)] transition-all">
                                    
                                        <!-- Card head -->
                                        <div class="flex items-center  justify-between pt-5 pb-3 mx-5 mb-2 border-b border-gray-200">
                                            <div class="overflow-hidden">
                                                <h5 class="item-name-style-narrow" :title="item.itemName">
                                                    {{ item.itemName }}
                                                </h5>
                                                <p class="item-sku-style-narrow"  :title="item.sku">
                                                    {{ item.sku ?? 'Not set' }}
                                                </p>
                                            </div>

                                            <div class="justify-center ml-auto w-fit">
                                                <button
                                                    @click="toggleSelectItem(null, item.id, item.itemName, item.price, item.unit, item.sku, item.category)"
                                                    :class=" selectedItemIds.includes(item.id) ? 'text-white bg-red-500' : 'border-2  border-[#45a049] text-[#45a049] bg-[#eef3ee]' " 
                                                    class="flex items-center justify-center rounded-full size-12"
                                                    type="button" 
                                                >
                                                    <i :class="{'rotate-45': selectedItemIds.includes(item.id)}" class="text-lg transition duration-500 transform fa-solid fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Card body -->
                                        <div class="grid gap-2 px-5 pt-2 pb-5 cursor-default">

                                            <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                <p class="label-style-two">Quantity</p>
                                                <span class="item-quantity-style-narrow ml-auto" :class="getStockColor(item)">
                                                    {{ item.quantity + ' ' + (item.unit ?? ( item.quantity < 2 ? 'unit' : 'units') ) }}
                                                </span>
                                            </div>
                                            
                                            <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                <p class="label-style-two">Price</p>
                                                <p class="ml-auto item-price-style-narrow">
                                                    ₱{{ item.price }}
                                                </p>
                                            </div>
                                            
                                            <div class="flex gap-4 h-fit overflow-hidden flex-row items-center ">
                                                <p class="label-style-two">Category</p>
                                                <p class="ml-auto item-category-style-narrow" :title="item.category">
                                                    {{ item.category ?? 'Not Set' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Modal>
    
    <teleport to="body">
        <Transition
            :enter-from-class="alertLoadingTransition.enterFrom"
            :enter-to-class="alertLoadingTransition.enterTo"
            :enter-active-class="alertLoadingTransition.enterActive"
            :leave-from-class="alertLoadingTransition.leaveFrom"
            :leave-to-class="alertLoadingTransition.leaveTo"
            :leave-active-class="alertLoadingTransition.leaveActive"
        >
            <Loading v-if="loading"></Loading>
        </Transition>

        <Transition
            :enter-from-class="alertLoadingTransition.enterFrom"
            :enter-to-class="alertLoadingTransition.enterTo"
            :enter-active-class="alertLoadingTransition.enterActive"
            :leave-from-class="alertLoadingTransition.leaveFrom"
            :leave-to-class="alertLoadingTransition.leaveTo"
            :leave-active-class="alertLoadingTransition.leaveActive"
        >
            <SuccessBanner @closeSuccess="closeSuccessMsg" v-if="hasSuccessMsg">
                <template v-slot:title>
                    {{ successMsgTitle }} 
                </template>

                <template v-slot:body>
                    {{ successMsgBody }}
                </template>
            </SuccessBanner>
        </Transition>

        <Transition
            :enter-from-class="alertLoadingTransition.enterFrom"
            :enter-to-class="alertLoadingTransition.enterTo"
            :enter-active-class="alertLoadingTransition.enterActive"
            :leave-from-class="alertLoadingTransition.leaveFrom"
            :leave-to-class="alertLoadingTransition.leaveTo"
            :leave-active-class="alertLoadingTransition.leaveActive"
        >
            <ErrorBanner @closeError="closeErrorMsg" v-if="hasErrorMsg">
                <template v-slot:title>
                    Error 
                </template>

                <template v-slot:body>
                    {{ errorMsgBody }}
                </template>
            </ErrorBanner>
        </Transition>
    </teleport>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { usePopUpTransition, useMainContentTransition, useAlertLoadingTransition, useFormTransition } from '../composables/useTransition';
import { useAlertMessages } from '../composables/useAlertMessages';
import { useFormatDate } from '../composables/useFormatDate';
import axiosClient from '../axios';
import useUserStore from '../store/user';
import Modal from '../components/Modal.vue';
import Loading from '../components/alerts/Loading.vue';
import SuccessBanner from '../components/alerts/SuccessBanner.vue';
import ErrorBanner from '../components/alerts/ErrorBanner.vue';
import ButtonDark from '../components/buttons/ButtonDark.vue';
import ButtonRed from '../components/buttons/ButtonRed.vue';
import ButtonYellow from '../components/buttons/ButtonYellow.vue';
import ButtonWhite from '../components/buttons/ButtonWhite.vue';
import TextDisplay from '../components/TextDisplay.vue';
import SelectDropdown from '../components/SelectDropdown.vue';
import TextArea from '../components/TextArea.vue';

const { 
    showSuccessMsg, 
    closeSuccessMsg, 
    showErrorMsg, 
    closeErrorMsg, 
    hasSuccessMsg, 
    hasErrorMsg, 
    successMsgTitle, 
    successMsgBody, 
    errorMsgBody 
} = useAlertMessages()

const { formatDate } = useFormatDate()
const userStore = useUserStore()
const user = computed(() => userStore.user)

const alertLoadingTransition = useAlertLoadingTransition()
const mainContentTransition = useMainContentTransition()
const formTransition = useFormTransition()

const viewingSalesList = ref(null)
const viewingUpdateSale = ref(null)
const viewingPaySale = ref(null)
const viewingReturnSale = ref(null)
const viewingSaleDetails = ref(null)
const viewingCancelSale = ref(null)
const viewingItemsListModal = ref(false)
const viewingCustomersMenu = ref(false)
const pageContent = ref(null)

const searchSaleValue = ref("")
const searchItemValue = ref("")
const currentSaleId = ref(null)
const loading = ref(false)
const selectedItemIds = ref([])
const customersListData = ref([])

const salesListData = ref([])
const dataStatus = ref(null)
const filteredSalesListData = computed(() => {
    return salesListData.value.filter(sale => {
        return sale.status.toLowerCase().includes(searchSaleValue.value.toLowerCase()) ||
            sale.soNumber.toLowerCase().includes(searchSaleValue.value.toLowerCase())
    })
})

const itemsListData = ref([])
const itemsListDataStatus = ref(null)
const filteredItemsListData = computed(() => {
    return itemsListData.value.filter(item => {
        return item.itemName.toLowerCase().includes(searchItemValue.value.toLowerCase()) ||
            item.sku.toLowerCase().includes(searchItemValue.value.toLowerCase())
    })
})

const colors = ref({
    low: null,
    zero: null
})

function getStockColor(item){
    if (item.quantity < 1){
        return colors.value.zero
    }
    else if(item.quantity <= item.reorderLevel){
        return colors.value.low
    }
    else {
        return "bg-emerald-500"
    }
}

const colorClass = ref({
    red: 'bg-red-500',
    yellow: 'bg-yellow-500',
    orange: 'bg-orange-500',
    green: 'bg-green-500',
    blue: 'bg-blue-500',
    violet: 'bg-violet-500',
    pink: 'bg-pink-500',
    gray: 'bg-gray-500',
})

const updateFormData = ref({
    hdrId: null,
    soNumber: null,
    remarks: null,
    createdDate: null,
    status: null,
    createdByName: null,
    customerId: null,
    customerName: null,
    selectedItemsList: ref([]),
    removedDtlIds: ref([])
})

const payFormData = ref({
    userId: null,
    userName: null,
    hdrId: null,
    soNumber: null,
    remarks: null,
    createdDate: null,
    status: null,
    createdByName: null,
    customerId: null,
    customerName: null,
    selectedItemsList: ref([]),
})

const returnFormData = ref({
    hdrId: null,
    soNumber: null,
    remarks: null,
    createdDate: null,
    status: null,
    createdByName: null,
    confirmedByName: null,
    confirmedDate: null,
    customerId: null,
    customerName: null,
    selectedItemsList: ref([]),
})

const saleDetails = ref({
    hdrId: null,
    soNumber: null,
    remarks: null,
    createdDate: null,
    status: null,
    createdByName: null,
    confirmedByName: null,
    confirmedDate: null,
    customerId: null,
    customerName: null,
    selectedItemsList: ref([]),
})

const cancelFormData = ref({
    hdrId: null,
    soNumber: null,
    remarks: null,
    createdDate: null,
    status: null,
    createdByName: null,
    customerId: null,
    customerName: null,
    selectedItemsList: ref([]),
})

async function getSalesListData() {
    dataStatus.value = "Loading..."

    try {
        const response = await axiosClient.get('/api/sales-list')    

        salesListData.value = response.data.salesListData
        customersListData.value = response.data.customersListData
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to load sale orders list data')
    }
    finally{
        if(salesListData.value.length < 1){
            dataStatus.value = "No data"
        }else{
            dataStatus.value = null
        }        
    }
}

async function getItemsListData() {
    itemsListDataStatus.value = "Loading..."

    try {
        const response = await axiosClient.get('/api/items-list')

        itemsListData.value = response.data.itemsListData

        const lowStockColor = response.data.colors?.lowStockColor?.color_name
        if(lowStockColor){
            colors.value.low = colorClass.value[lowStockColor]
        }else{
            colors.value.low = "bg-emerald-500"
        }

        const zeroStockColor = response.data.colors?.noStockColor?.color_name
        if(zeroStockColor){
            colors.value.zero = colorClass.value[zeroStockColor]
        }else{
            colors.value.zero = "bg-emerald-500"
        }
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to load items list data')
    }
    finally{
        if(itemsListData.value.length < 1){
            itemsListDataStatus.value = "No data"
        }else{
            itemsListDataStatus.value = null
        }        
    }
}

async function updateSale() {
    loading.value = true

    try {
        const response = await axiosClient.put('/api/update-sale', updateFormData.value)
        
        showSuccessMsg("Success", response.data.message)
        await getSalesListData()
        showSalesList()
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to update sale order')
    }
    finally{
        loading.value = false
    }
}

async function paySale() {
    loading.value = true

    try {
        const response = await axiosClient.put('/api/pay-sale', payFormData.value)
        
        showSuccessMsg("Success", response.data.message)
        await getSalesListData()
        showSalesList()
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to pay sale order')
    }
    finally{
        loading.value = false
    }
}

async function returnSale(){
    loading.value = true

    try {
        const response = await axiosClient.put('/api/return-sale', returnFormData.value)
        
        showSuccessMsg("Success", response.data.message)
        await getSalesListData()
        showSalesList()
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to return sale order')
    }
    finally{
        loading.value = false
    }
}

async function cancelSale() {
    loading.value = true

    try {
        const response = await axiosClient.put('/api/cancel-sale', cancelFormData.value)    

        showSuccessMsg("Success", response.data.message)
        await getSalesListData()
        showSalesList()
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to cancel sale order')
    }
    finally{
        loading.value = false
    }
}

async function showUpdateForm(sale) {
    loading.value = true
    updateFormData.value.hdrId = sale.hdrId
    updateFormData.value.soNumber = sale.soNumber
    updateFormData.value.createdDate = sale.createdDate
    updateFormData.value.remarks = sale.remarks
    updateFormData.value.status = sale.status
    updateFormData.value.createdByName = sale.createdByName
    updateFormData.value.customerId = sale.customerId
    updateFormData.value.customerName = sale.customerName

    try {
        const response = await axiosClient.get('/api/sale-dtls', {params: { hdrId: sale.hdrId}})   
        
        updateFormData.value.selectedItemsList = response.data.sale_details

        updateFormData.value.selectedItemsList.forEach(selectedItem => {
            selectedItemIds.value.push(selectedItem.itemId)
        })

        viewingSalesList.value = false
        pageContent.value = "Update Sale"
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to fetch sale order details')   
    }
    finally{
        loading.value = false
    }
}

async function showPayForm(sale) {
    loading.value = true
    payFormData.value.hdrId = sale.hdrId
    payFormData.value.soNumber = sale.soNumber
    payFormData.value.createdDate = sale.createdDate
    payFormData.value.remarks = sale.remarks
    payFormData.value.status = sale.status
    payFormData.value.createdByName = sale.createdByName
    payFormData.value.customerId = sale.customerId
    payFormData.value.customerName = sale.customerName

    try {
        const response = await axiosClient.get('/api/sale-dtls', {params: { hdrId: sale.hdrId }})  
        
        payFormData.value.selectedItemsList = response.data.sale_details

        viewingSalesList.value = false
        pageContent.value = "Pay Sale"
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to fetch sale order details')  
    }
    finally{
        loading.value = false
    }
}

async function showReturnForm(sale) {
    loading.value = true
    returnFormData.value.hdrId = sale.hdrId
    returnFormData.value.soNumber = sale.soNumber
    returnFormData.value.createdDate = sale.createdDate
    returnFormData.value.remarks = sale.remarks
    returnFormData.value.status = sale.status
    returnFormData.value.createdByName = sale.createdByName
    returnFormData.value.confirmedByName = sale.confirmedByName
    returnFormData.value.confirmedDate = sale.confirmedDate
    returnFormData.value.customerId = sale.customerId
    returnFormData.value.customerName = sale.customerName

    try {
        const response = await axiosClient.get('/api/sale-dtls', {params: { hdrId: sale.hdrId }}) 
        
        returnFormData.value.selectedItemsList = response.data.sale_details

        viewingSalesList.value = false
        pageContent.value = "Return Sale"
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to fetch sale order details')  
    }
    finally{
        loading.value = false
    } 
}

async function showCancelForm(sale) {
    loading.value = true
    cancelFormData.value.hdrId = sale.hdrId
    cancelFormData.value.soNumber = sale.soNumber
    cancelFormData.value.remarks = sale.remarks
    cancelFormData.value.createdDate = sale.createdDate
    cancelFormData.value.status = sale.status
    cancelFormData.value.createdByName = sale.createdByName
    cancelFormData.value.customerId = sale.customerId
    cancelFormData.value.customerName = sale.customerName

    try {
        const response = await axiosClient.get('/api/sale-dtls', {params: { hdrId: sale.hdrId}})    

        cancelFormData.value.selectedItemsList = response.data.sale_details

        viewingSalesList.value = false
        pageContent.value = "Cancel Sale"
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to fetch sale order details')  
    }
    finally{
        loading.value = false
    }
}

async function showSaleDetails(sale) {
    loading.value = true
    saleDetails.value.hdrId = sale.hdrId
    saleDetails.value.soNumber = sale.soNumber
    saleDetails.value.createdDate = sale.createdDate
    saleDetails.value.remarks = sale.remarks
    saleDetails.value.status = sale.status
    saleDetails.value.createdByName = sale.createdByName
    saleDetails.value.confirmedByName = sale.confirmedByName
    saleDetails.value.confirmedDate = sale.confirmedDate
    saleDetails.value.customerId = sale.customerId
    saleDetails.value.customerName = sale.customerName

    try {
        const response = await axiosClient.get('/api/sale-dtls', {params: { hdrId: sale.hdrId}})  
        
        saleDetails.value.selectedItemsList = response.data.sale_details

        viewingSalesList.value = false
        pageContent.value = "View Sale Details"
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to fetch sale order details')   
    }
    finally{
        loading.value = false
    }
}

function changeContent(content){
    if(content === 'Sales List'){
        viewingSalesList.value = true
    }else if(content === 'Update Sale'){
        viewingUpdateSale.value = true
    }else if(content === "Pay Sale"){
        viewingPaySale.value = true
    }else if(content === "Return Sale"){
        viewingReturnSale.value = true
    }else if(content === "View Sale Details"){
        viewingSaleDetails.value = true
    }else if(content === "Cancel Sale"){
        viewingCancelSale.value = true
    }else{
        return
    }

    mainContentTransition.value.enterFrom = "-translate-x-1/2 opacity-0"
}

function showSalesList(){
    viewingUpdateSale.value = false
    viewingPaySale.value = false
    viewingReturnSale.value = false
    viewingSaleDetails.value = false
    viewingCancelSale.value = false

    updateFormData.value.hdrId = null
    updateFormData.value.remarks = null
    updateFormData.value.createdDate = null
    updateFormData.value.status = null
    updateFormData.value.createdByName = null
    updateFormData.value.customerId = null
    updateFormData.value.customerName = null
    updateFormData.value.selectedItemsList = []
    updateFormData.value.removedDtlIds = []

    payFormData.value.hdrId = null
    payFormData.value.remarks = null
    payFormData.value.createdDate = null
    payFormData.value.status = null
    payFormData.value.createdByName = null
    payFormData.value.customerId = null
    payFormData.value.customerName = null
    payFormData.value.selectedItemsList = []

    returnFormData.value.hdrId = null
    returnFormData.value.remarks = null
    returnFormData.value.createdDate = null
    returnFormData.value.status = null
    returnFormData.value.createdByName = null
    returnFormData.value.confirmedByName = null
    returnFormData.value.confirmedDate = null
    returnFormData.value.customerId = null
    returnFormData.value.customerName = null
    returnFormData.value.selectedItemsList = []

    saleDetails.value.hdrId = null
    saleDetails.value.remarks = null
    saleDetails.value.createdDate = null
    saleDetails.value.status = null
    saleDetails.value.createdByName = null
    saleDetails.value.confirmedByName = null
    saleDetails.value.confirmedDate = null
    saleDetails.value.customerId = null
    saleDetails.value.customerName = null
    saleDetails.value.selectedItemsList = []

    cancelFormData.value.hdrId = null
    cancelFormData.value.remarks = null
    cancelFormData.value.createdDate = null
    cancelFormData.value.status = null
    cancelFormData.value.createdByName = null
    cancelFormData.value.customerId = null
    cancelFormData.value.customerName = null
    cancelFormData.value.selectedItemsList = []

    selectedItemIds.value = []
}

function showItemsListModal(){
    viewingItemsListModal.value = true
    getItemsListData()
}

function toggleMoreActions (id){
    if (currentSaleId.value == id){
        currentSaleId.value = null
    }else{
        currentSaleId.value = id
    } 
}

function toggleSelectItem(dtlId, itemId, itemName, itemPrice, itemUnit, itemSKU, itemCategory){

    if (selectedItemIds.value.includes(itemId)){
        
        // Add to updateFormData.removedDtlIds
        updateFormData.value.selectedItemsList.forEach(selectedItem => {
            if(selectedItem.itemId === itemId){
                if(selectedItem.dtlId){
                    updateFormData.value.removedDtlIds.push(selectedItem.dtlId)
                }
            }
        })

        // Remove from updateFormData.selectedItemsList
        updateFormData.value.selectedItemsList = updateFormData.value.selectedItemsList.filter(selectedItem => {
            return selectedItem.itemId != itemId
        });

        // Remove from selectedItemIds
        selectedItemIds.value = selectedItemIds.value.filter(i => {
            return i != itemId
        })

    }
    else{
        updateFormData.value.selectedItemsList.push({
            dtlId: null,
            itemId: itemId, 
            itemName: itemName, 
            sku: itemSKU,
            unit: itemUnit,
            price: itemPrice,
            quantity: 1,
            category: itemCategory,
        })

        selectedItemIds.value.push(itemId)
    }
}

function selectCustomer(customer){
    if(customer){
        updateFormData.value.customerId = customer.id
        updateFormData.value.customerName = customer.name
    }else{
        updateFormData.value.customerId = null
        updateFormData.value.customerName = null
    }
}

function handleClickOutside(e) {
    if (!e.target.closest(".btnActions")) {
        currentSaleId.value = null
    }
    
    if(!e.target.closest('.customers-dropdown')){
        viewingCustomersMenu.value = false
    }
}

onMounted(() => {
    document.addEventListener("click", handleClickOutside)

    getSalesListData()
    viewingSalesList.value = true

    payFormData.value.userId = user.value.id
    payFormData.value.userName = user.value.name
})

onBeforeUnmount(() => {
    document.removeEventListener("click", handleClickOutside)
})

</script>

<style scoped>

</style>