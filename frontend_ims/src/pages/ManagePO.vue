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
            <section v-show="viewingPOsList" class=" h-full flex flex-col max-h-[1200px] min-h-[800px] lg:min-h-[600px] w-full max-w-[1300px] mx-auto">
                <div class="flex flex-col items-start px-2 mt-2 mb-4">
                    <h3 class="font-semibold tracking-wider">Manage Purchase Orders</h3>
                </div>

                <div class="flex flex-1 p-2 overflow-hidden">
                    <div class="flex flex-col flex-1 overflow-hidden min-h-[350px] bg-white rounded-xl shadow-[0px_1px_4px_rgba(0,0,0,0.1)] p-6">
                        
                        <!-- Search bar -->
                        <div class="flex mb-4">
                            <input 
                                v-model="searchPOValue" 
                                type="text" 
                                placeholder="Search by PO number or status" 
                                class="search-bar"
                            >
                        </div>
                        
                        <div class="flex flex-col flex-1 pb-2 overflow-hidden">

                            <!-- Desktop table layout -->
                            <div class="relative flex-1 hidden overflow-hidden lg:flex">

                                <!-- Gradient overlay -->
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                                
                                <div class="flex-1 px-1 overflow-auto ">
                                    <div class="grid grid-cols-[25%_20%_25%_20%_10%] head-row">
                                        <div class="head-data">PO Number</div>
                                        <div class="head-data">Ordered Date</div>
                                        <div class="head-data">Remarks</div>
                                        <div class="head-data">Status</div>
                                        <div class="justify-center head-data">Action</div>
                                    </div>

                                    <div v-if="dataStatus" class="flex items-center justify-center w-full py-3">
                                        <p>{{ dataStatus }}</p>
                                    </div>

                                    <div v-if="!dataStatus" v-for="po in filteredPOsListData" class="grid grid-cols-[25%_20%_25%_20%_10%] body-row">
                                        <div class="body-data po-number-style-wide">
                                            {{ po.poNumber }}
                                        </div>
                                        <div class="body-data po-date-style-wide">
                                            {{ formatDate(po.orderDate) }}
                                        </div>
                                        <div class="body-data">
                                            <div class="po-remarks-style-wide" :title="po.remarks">
                                                {{ po.remarks ?? 'no remarks' }}
                                            </div>
                                        </div>
                                        <div class="body-data">
                                            <div 
                                                :class="['px-3 py-1 w-fit text-xs font-medium rounded text-nowrap h-fit uppercase', 
                                                    po.status == 'Pending' ? 'bg-yellow-100 text-yellow-600' : '', 
                                                    po.status == 'Received' ? 'bg-blue-100 text-blue-600' : '',
                                                    po.status == 'Cancelled' ? 'bg-red-100 text-red-600' : '', 
                                                    po.status == 'Returned' ? 'bg-orange-100 text-orange-600' : ''
                                                ]"
                                            >
                                                {{ po.status }}
                                            </div>
                                        </div>
                                        <div class="flex justify-center items-center body-data">
                                            <button 
                                                @click="toggleMoreActions(po.hdrId)" 
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
                                                <div v-if="currentPOId == po.hdrId" class="absolute inset-0 flex items-center justify-end w-full h-full ">
                                                    <div class="bg-gradient-to-r flex-1 h-full from-[rgba(0,0,0,0.01)] to-darkgray-pri "></div>
                                                    
                                                    <div class="flex items-center justify-end h-full p-2 bg-darkgray-pri w-fit btnActions">
                                                        <button
                                                            @click="showPODetails(po)"
                                                            class="h-full px-4 font-semibold text-white rounded-md w-fit hover:bg-darkgray-sec"
                                                            type="button"
                                                            width="full" 
                                                            title="View Details"
                                                        >
                                                            View
                                                        </button>

                                                        <button 
                                                            v-if="po.status == 'Pending'"
                                                            @click="showReceivingForm(po)"
                                                            class="h-full px-4 font-semibold text-white rounded-md w-fit hover:bg-darkgray-sec"
                                                            type="button"
                                                            width="full" 
                                                            title="Receive"
                                                        >
                                                            Receive
                                                        </button>

                                                        <button 
                                                            v-if="po.status == 'Pending'"
                                                            @click="showPendingUpdateForm(po)"
                                                            class="h-full px-4 font-semibold text-white rounded-md w-fit hover:bg-darkgray-sec"
                                                            type="button"
                                                            width="full" 
                                                            title="Update"
                                                        >
                                                            Update
                                                        </button>

                                                        <button
                                                            v-if="po.status == 'Pending'"
                                                            @click="showCancelForm(po)"
                                                            class="h-full px-4 font-semibold text-white rounded-md w-fit hover:bg-red-700 "
                                                            type="button" 
                                                            width="full" 
                                                            title="Cancel"
                                                        >
                                                            Cancel
                                                        </button>

                                                        <button 
                                                            v-if="po.status == 'Received'"
                                                            @click="showReceivedUpdateForm(po)"
                                                            class="h-full px-4 font-semibold text-white rounded-md w-fit hover:bg-darkgray-sec"
                                                            type="button"
                                                            width="full" 
                                                            title="Update"
                                                        >
                                                            Update
                                                        </button>

                                                        <button
                                                            v-if="po.status == 'Received'"
                                                            @click="showReturnForm(po)"
                                                            class="h-full px-4 font-semibold text-white rounded-md w-fit hover:bg-red-700 "
                                                            type="button"
                                                            width="full"  
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
                                    <div class="my-2 flex-1 gap-3 overflow-hidden min-h-fit content-start grid grid-cols-[repeat(auto-fill,minmax(250px,1fr))] p-1">
                                    
                                        <!-- Card items -->
                                        <div v-if="!dataStatus" v-for="po in filteredPOsListData" class="relative flex flex-col rounded-lg shadow-[0px_1px_4px_rgba(0,0,0,0.1)] max-h-full hover:shadow-[0_1px_4px_rgba(0,0,0,0.25)] transition-all">
                                            
                                            <!-- Card head -->
                                            <div class="flex flex-col items-start justify-between pt-5 pb-3 mx-5 mb-2 border-b border-gray-200">
                                                <div class="flex items-center w-full ">
                                                    <h5 class="po-number-style-narrow">
                                                        {{ po.poNumber }}
                                                    </h5>

                                                    <button 
                                                        @click="toggleMoreActions(po.hdrId)" 
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
                                                        :class="['px-3 py-1 w-fit text-xs font-medium rounded text-nowrap uppercase ml-auto', 
                                                            po.status == 'Pending' ? 'bg-yellow-100 text-yellow-600' : '', 
                                                            po.status == 'Received' ? 'bg-blue-100 text-blue-600' : '',
                                                            po.status == 'Cancelled' ? 'bg-red-100 text-red-600' : '', 
                                                            po.status == 'Returned' ? 'bg-orange-100 text-orange-600' : ''
                                                        ]"
                                                    >
                                                        {{ po.status }}
                                                    </div>
                                                    
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <div class="label-style-two">Date ordered:</div> 
                                                    <div class="ml-auto po-date-style-narrow">
                                                        {{ formatDate(po.orderDate) }}
                                                    </div>
                                                </div>
                                                
                                                <div class="po-remarks-style-narrow" :title="po.remarks">
                                                    {{ po.remarks ?? 'no remarks' }}
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
                                                <div v-if="currentPOId == po.hdrId" class="absolute flex flex-col items-center justify-center w-full h-full gap-1 rounded-lg bg-black/60 ">
                                                    <div class="grid w-3/5 grid-cols-1 gap-1 btnActions min-w-[150px]">
                                                        <button
                                                            @click="showPODetails(po)"
                                                            class="w-full px-4 py-2 bg-white rounded-lg hover:shadow-[0_0_6px] hover:shadow-white/70"
                                                            type="button"
                                                            width="full" 
                                                            title="View Details"
                                                        >
                                                            View Details
                                                        </button>

                                                        <button
                                                            v-if="po.status == 'Pending'"
                                                            @click="showReceivingForm(po)"
                                                            class="w-full px-4 py-2 bg-white rounded-lg hover:shadow-[0_0_6px] hover:shadow-white/70" 
                                                            type="button"
                                                            width="full"
                                                            title="Receive"
                                                        >
                                                            Recieve
                                                        </button>

                                                        <button
                                                            v-if="po.status == 'Pending'"
                                                            @click="showPendingUpdateForm(po)"
                                                            class="w-full px-4 py-2 bg-white rounded-lg hover:shadow-[0_0_6px] hover:shadow-white/70" 
                                                            type="button"
                                                            width="full"
                                                            title="Update"
                                                        >
                                                            Update
                                                        </button>

                                                        <button
                                                            v-if="po.status == 'Pending'"
                                                            @click="showCancelForm(po)"
                                                            class="w-full px-4 py-2 bg-white text-red-700 rounded-lg hover:shadow-[0_0_6px] hover:shadow-red-500/90"
                                                            type="button"
                                                            width="full" 
                                                            title="Cancel"
                                                        >
                                                            Cancel
                                                        </button>

                                                        <button
                                                            v-if="po.status == 'Received'"
                                                            @click="showReceivedUpdateForm(po)"
                                                            class="w-full px-4 py-2 bg-white rounded-lg hover:shadow-[0_0_6px] hover:shadow-white/70" 
                                                            type="button"
                                                            width="full"
                                                            title="Update"
                                                        >
                                                            Update
                                                        </button>

                                                        <button
                                                            v-if="po.status == 'Received'"
                                                            @click="showReturnForm(po)"
                                                            class="w-full px-4 py-2 bg-white text-red-700 rounded-lg hover:shadow-[0_0_6px] hover:shadow-red-500/90"
                                                            type="button" 
                                                            width="full"
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

        <!-- Update form -->
        <transition
            :enter-from-class="formTransition.enterFrom"
            :enter-to-class="formTransition.enterTo"
            :enter-active-class="formTransition.enterActive"
            :leave-from-class="formTransition.leaveFrom"
            :leave-to-class="formTransition.leaveTo"
            :leave-active-class="formTransition.leaveActive"
            @after-leave="changeContent('Purchase Orders')"
        >
            <div v-show="viewingPendingUpdateForm" class="flex flex-col w-full max-h-[1500px] min-h-[1400px] lg:max-h-[1300px] lg:min-h-[1200px] h-full max-w-[1300px] mx-auto ">
                <form @submit.prevent="updatePendingPO" class="flex flex-col flex-1 w-full p-2 mx-auto overflow-hidden">
                    <section class="shadow-[0px_1px_4px_rgba(0,0,0,0.1)] overflow-hidden flex-1 p-6 sm:p-8 bg-white rounded-xl flex flex-col mb-4">
                        <div class="flex flex-row items-start">
                            <div class="flex flex-col">
                                <h3 class="font-bold text-left px-1">Update Purchase Order</h3>
                                <p class="mb-5 text-sm text-gray-500 px-1">Update purchase order details</p>
                            </div>
                            
                            <p class="px-3 py-1 ml-auto font-medium text-yellow-600 bg-yellow-100 rounded w-fit text-nowrap">
                                {{ "Status: " + pendingUpdateFormData.status }}
                            </p>
                        </div>

                        <hr class="mb-5 mx-1">

                        <div class="mb-5 flex items-center gap-2 text-lg">
                            <i class="fas fa-info-circle"></i>
                            <span class="mr-2 font-medium">Order Details</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-x-4">
                            <TextDisplay
                                label="PO Number:"
                                :value="pendingUpdateFormData.poNumber"
                            />

                            <TextDisplay
                                label="Date Ordered:"
                                :value="formatDate(pendingUpdateFormData.orderDate)"
                            />
                        </div>

                        <SelectDropdown
                            v-model="pendingUpdateFormData.supplierId"
                            label="Supplier:"
                            :listData="suppliersListData"
                        />

                        <TextArea
                            v-model="pendingUpdateFormData.remarks" 
                            id="remarks-update-pending" 
                            label="Remarks:"
                        />

                        <TextDisplay
                            label="Created by:"
                            :value="pendingUpdateFormData.createdByName"
                        />

                        <hr class="mt-2 mb-5 mx-1">

                        <div class="flex flex-col flex-1 overflow-hidden">

                            <div class="mb-5 flex flex-row flex-wrap items-end justify-between gap-y-2">
                                <div class="flex items-center gap-2 text-lg">
                                    <i class="fas fa-list"></i>
                                    <span class="mr-2 font-medium">Ordered Items</span>
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
                                        <div class="head-data">Cost</div>
                                        <div class="head-data">Quantity</div>
                                        <div class="head-data">Subtotal</div>
                                        <div class="justify-center head-data">Action</div>
                                    </div>
                                    
                                    <div v-for="item in pendingUpdateFormData.selectedItemsList" class="grid grid-cols-[auto_20%_25%_20%_80px] body-row">
                                        <div class="grid body-data">
                                            <p class="item-name-style-wide" :title="item.itemName">
                                                {{ item.itemName }}
                                            </p>
                                            <p class="item-sku-style-wide" :title="item.sku">
                                                {{ item.sku }}
                                            </p>
                                        </div>
                                        <div class="body-data item-cost-style-wide">
                                            ₱{{ item.cost }}
                                        </div>
                                        <div class="body-data">
                                            <div class="flex flex-row w-full border border-gray-300 rounded-lg">
                                                <input 
                                                    v-model="item.orderedQty" 
                                                    type="text" 
                                                    class="flex-1 min-w-0 px-3 py-1.5 text-right text-sm bg-transparent focus:outline-none"
                                                    placeholder="0"
                                                >
                                                <span class="grid px-3 text-gray-500 border-l border-gray-300 whitespace-nowrap place-items-center">
                                                    {{ item.unit ?? (item.orderedQty < 2 ? 'unit' : 'units') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="body-data item-subtotal-style-wide">
                                            ₱{{ (item.cost * item.orderedQty).toFixed(2)  }}
                                        </div>
                                        <div class="justify-center body-data">
                                            <button 
                                                @click="toggleSelectItem(item.dtlId, item.itemId, item.itemName, item.cost, item.unit, item.sku)" 
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
                                        <div v-for="item in pendingUpdateFormData.selectedItemsList" class="cursor-default flex flex-col shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-lg max-h-full hover:shadow-[0_1px_4px_rgba(0,0,0,0.25)] transition-all">
                                        
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

                                                <div class="justify-center ml-auto w-fit">
                                                    <button 
                                                        @click="toggleSelectItem(item.dtlId, item.itemId, item.itemName, item.cost, item.unit, item.sku)" 
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
                                                    <p class="label-style-two">Cost</p>
                                                    <p class="ml-auto item-cost-style-narrow">
                                                        ₱{{ item.cost }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="w-1/2 label-style-two">Quantity</p>
                                                    <div class="flex items-center justify-between w-1/2 transition bg-white border border-gray-300 rounded-md">
                                                        <input 
                                                            v-model="item.orderedQty"
                                                            class="flex-1 min-w-0 px-1 py-1.5 text-right text-sm bg-transparent"
                                                            type="number"
                                                            placeholder="0"
                                                        >
                                                        <span class="px-3 text-sm text-gray-500 border-l border-gray-200 whitespace-nowrap">
                                                            {{ item.unit ?? (item.orderedQty < 2 ? 'unit' : 'units') }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center pt-2 border-t">
                                                    <p class="label-style-two">Subtotal</p>
                                                    <p class="ml-auto item-subtotal-style-narrow">
                                                        ₱{{ (item.cost * item.orderedQty).toFixed(2) }}
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

                            <ButtonWhite @click="showPOsList" width="full" type="button">
                                Cancel
                            </ButtonWhite>
                        </div>
                    </div>
                </form>
            </div>
        </transition>

        <!-- Cancel form -->
        <transition
            :enter-from-class="formTransition.enterFrom"
            :enter-to-class="formTransition.enterTo"
            :enter-active-class="formTransition.enterActive"
            :leave-from-class="formTransition.leaveFrom"
            :leave-to-class="formTransition.leaveTo"
            :leave-active-class="formTransition.leaveActive"
            @after-leave="changeContent('Purchase Orders')"
        >
            <div v-show="viewingCancelForm" class="flex flex-col w-full max-h-[1500px] min-h-[1400px] lg:max-h-[1300px] lg:min-h-[1200px] h-full max-w-[1300px] mx-auto ">
                <form @submit.prevent="cancelPO" class="flex flex-col flex-1 w-full p-2 mx-auto overflow-hidden">
                    <section class="shadow-[0px_1px_4px_rgba(0,0,0,0.1)] overflow-hidden flex-1 p-6 sm:p-8 bg-white rounded-xl flex flex-col mb-4">
                        <div class="flex flex-row items-start">
                            <div class="flex flex-col">
                                <h3 class="font-bold text-left px-1">Cancel Purchase Order</h3>
                                <p class="mb-5 text-sm text-gray-500 px-1">Confirm cancellation of the purchase order</p>
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
                                label="PO Number:"
                                :value="cancelFormData.poNumber"
                            />
                            <TextDisplay
                                label="Date Ordered:"
                                :value="formatDate(cancelFormData.orderDate)"
                            />
                        </div>

                        <TextDisplay
                            label="Supplier:"
                            :value="cancelFormData.supplierName"
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
                                <span class="mr-2 font-medium">Ordered Items</span>
                            </div>

                            <!-- Desktop table layout -->
                            <div class="relative flex-1 hidden overflow-hidden lg:flex">
                                
                                <!-- Gradient overlay -->
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                            
                                <div class="flex-1 px-1 overflow-auto">
                                    <div class="grid grid-cols-[40%_20%_20%_20%] head-row">
                                        <div class="head-data">Item</div>
                                        <div class="head-data">Cost</div>
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
                                        <div class="body-data item-cost-style-wide">
                                            ₱{{ item.cost }}
                                        </div>
                                        <div class="body-data">
                                            <div class="flex flex-row text-gray-500 bg-gray-100 rounded-md h-fit">
                                                <p class="grid px-3  py-1.5 place-items-center">
                                                    {{ item.orderedQty + " " + (item.unit ?? (item.orderedQty < 2 ? 'unit' : 'units')) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="body-data item-subtotal-style-wide">
                                            ₱{{ (item.cost * item.orderedQty).toFixed(2)  }}
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
                                        <div v-for="item in cancelFormData.selectedItemsList" class="cursor-default flex flex-col shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-lg max-h-full hover:shadow-[0_1px_4px_rgba(0,0,0,0.25)] transition-all">
                                        
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
                                                    <p class="label-style-two">Cost</p>
                                                    <p class="ml-auto item-cost-style-narrow">
                                                        ₱{{ item.cost }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="w-1/2 label-style-two">Ordered</p>
                                                    <p class="w-1/2 ml-auto text-sm px-3 py-1.5  text-gray-500 text-right bg-gray-100 rounded-md">
                                                        {{ item.orderedQty + " " + ( item.unit ?? (item.orderedQty < 2 ? 'unit' : 'units') ) }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center pt-2 border-t">
                                                    <p class="label-style-two">Subtotal</p>
                                                    <p class="ml-auto item-subtotal-style-narrow">
                                                        ₱{{ (item.cost * item.orderedQty).toFixed(2) }}
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
                                Cancel PO
                            </ButtonRed>

                            <ButtonWhite @click="showPOsList" width="full" type="button">
                                Keep PO
                            </ButtonWhite>
                        </div>
                    </div>
                </form>
            </div>
        </transition>

        <!-- Receive form -->
        <transition
            :enter-from-class="formTransition.enterFrom"
            :enter-to-class="formTransition.enterTo"
            :enter-active-class="formTransition.enterActive"
            :leave-from-class="formTransition.leaveFrom"
            :leave-to-class="formTransition.leaveTo"
            :leave-active-class="formTransition.leaveActive"
            @after-leave="changeContent('Purchase Orders')"
        >
            <div v-show="viewingReceiveForm" class="flex flex-col w-full max-h-[1500px] min-h-[1500px] lg:max-h-[1300px] lg:min-h-[1200px] h-full max-w-[1300px] mx-auto ">
                <form @submit.prevent="receivePO" class="flex flex-col flex-1 w-full p-2 mx-auto overflow-hidden">
                    <section class="shadow-[0px_1px_4px_rgba(0,0,0,0.1)] overflow-hidden flex-1 p-6 sm:p-8 bg-white rounded-xl flex flex-col mb-4">
                        <div class="flex flex-row items-start">
                            <div class="flex flex-col">
                                <h3 class="font-bold text-left px-1">Receive Purchase Order</h3>
                                <p class="mb-5 text-sm text-gray-500 px-1">Confirm receipt of the purchase order</p>
                            </div>
                            
                            <p class="px-3 py-1 ml-auto font-medium text-yellow-600 bg-yellow-100 rounded w-fit text-nowrap">
                                {{ "Status: " + receivingFormData.status }}
                            </p>
                        </div>

                        <hr class="mb-5 mx-1">

                        <div class="mb-5 flex items-center gap-2 text-lg">
                            <i class="fas fa-info-circle"></i>
                            <span class="mr-2 font-medium">Order Details</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-x-4">
                            <TextDisplay
                                label="PO Number:"
                                :value="receivingFormData.poNumber"
                            />
                            <TextDisplay
                                label="Date Ordered:"
                                :value="formatDate(receivingFormData.orderDate)"
                            />
                        </div>

                        <TextDisplay
                            label="Supplier:"
                            :value="receivingFormData.supplierName"
                        />

                        <TextArea
                            v-model="receivingFormData.remarks" 
                            id="remarks-receive" 
                            label="Remarks:"
                        />

                        <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-x-4">
                            <TextDisplay
                                label="Created by:"
                                :value="receivingFormData.createdByName"
                            />

                            <TextDisplay
                                label="Received by:"
                                :value="receivingFormData.userName"
                            />
                        </div>

                        <hr class="mt-2 mb-5 mx-1">
                        
                        <div class="flex flex-col flex-1 overflow-hidden">
                            <div class="mb-5 flex items-center gap-2 text-lg">
                                <i class="fas fa-list"></i>
                                <span class="mr-2 font-medium">Ordered Items</span>
                            </div>

                            <!-- Desktop table layout -->
                            <div class="relative flex-1 hidden overflow-hidden lg:flex">
                                
                                <!-- Gradient overlay -->
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>

                                <div class="flex-1 px-1 overflow-auto">
                                    <div class="grid grid-cols-[30%_15%_20%_20%_15%] head-row">
                                        <div class="head-data">Item</div>
                                        <div class="head-data">Cost</div>
                                        <div class="head-data">Ordered Qty</div>
                                        <div class="head-data">Received Qty</div>
                                        <div class="head-data">Received Subtotal</div>
                                    </div>
                                    
                                    <div v-for="item in receivingFormData.selectedItemsList" class="grid grid-cols-[30%_15%_20%_20%_15%] body-row">
                                        <div class="grid body-data">
                                            <p class="item-name-style-wide" :title="item.itemName">
                                                {{ item.itemName }}
                                            </p>
                                            <p class="item-sku-style-wide" :title="item.sku">
                                                {{ item.sku }}
                                            </p>
                                        </div>
                                        <div class="body-data item-cost-style-wide">
                                            ₱{{ item.cost }}
                                        </div>
                                        <div class="body-data">
                                            <div class="text-gray-500 bg-gray-100 rounded-md h-fit">
                                                <p class="grid px-3  py-1.5 place-items-center">
                                                    {{ item.orderedQty + " " + (item.unit ?? (item.orderedQty < 2 ? 'unit' : 'units')) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="body-data">
                                            <div class="flex flex-row w-full border border-gray-300 rounded-lg">
                                                <input 
                                                    v-model="item.receivedQty" 
                                                    type="number" 
                                                    class="flex-1 min-w-0 px-3 py-1.5 text-right text-sm bg-transparent focus:outline-none"
                                                >
                                                <span class="grid px-3 text-gray-500 border-l border-gray-300 whitespace-nowrap place-items-center">
                                                    {{ item.unit ?? (item.receivedQty < 2 ? 'unit' : 'units') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="body-data item-subtotal-style-wide">
                                            ₱{{ (item.cost * item.receivedQty).toFixed(2) }}
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
                                        <div v-for="item in receivingFormData.selectedItemsList" class="cursor-default flex flex-col shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-lg max-h-full hover:shadow-[0_1px_4px_rgba(0,0,0,0.25)] transition-all">
                                        
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
                                                    <p class="label-style-two">Cost</p>
                                                    <p class="ml-auto item-cost-style-narrow">
                                                        ₱{{ item.cost }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="w-1/2 label-style-two">Ordered</p>
                                                    <p class="w-1/2 text-sm px-3 py-1.5  text-gray-500 text-right bg-gray-100 rounded-md">
                                                        {{ item.orderedQty + " " + ( item.unit ?? (item.orderedQty < 2 ? 'unit' : 'units') ) }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="w-1/2 label-style-two">Received</p>
                                                    <div class="flex items-center justify-between w-1/2 bg-white border border-gray-300 rounded-md">
                                                        <input 
                                                            v-model="item.receivedQty"
                                                            class="w-full min-w-0 px-1 py-1.5 text-right text-sm bg-transparent"
                                                            type="number"
                                                            placeholder="0"
                                                        >
                                                        <span class="px-3 text-sm text-gray-500 border-l border-gray-200 whitespace-nowrap">
                                                            {{ item.unit ?? (item.receivedQty < 2 ? 'unit' : 'units') }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center pt-2 border-t">
                                                    <p class="label-style-two">Received Subtotal</p>
                                                    <p class="ml-auto item-subtotal-style-narrow">
                                                        ₱{{ (item.cost * item.receivedQty).toFixed(2) }}
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
                                Receive
                            </ButtonYellow>

                            <ButtonWhite @click="showPOsList" width="full" type="button">
                                Cancel
                            </ButtonWhite>
                        </div>
                    </div>
                </form>
            </div>
        </transition>

        <!-- Return form -->
        <transition
            :enter-from-class="formTransition.enterFrom"
            :enter-to-class="formTransition.enterTo"
            :enter-active-class="formTransition.enterActive"
            :leave-from-class="formTransition.leaveFrom"
            :leave-to-class="formTransition.leaveTo"
            :leave-active-class="formTransition.leaveActive"
            @after-leave="changeContent('Purchase Orders')"
        >
            <div v-show="viewingReturnForm" class="flex flex-col w-full max-h-[1600px] min-h-[1600px] lg:max-h-[1300px] lg:min-h-[1200px] h-full max-w-[1300px] mx-auto ">
                <form @submit.prevent="returnPO" class="flex flex-col flex-1 w-full p-2 mx-auto overflow-hidden">
                    <section class="shadow-[0px_1px_4px_rgba(0,0,0,0.1)] overflow-hidden flex-1 p-6 sm:p-8 bg-white rounded-xl flex flex-col mb-4">
                        <div class="flex flex-row items-start">
                            <div class="flex flex-col">
                                <h3 class="font-bold text-left px-1">Return Purchase Order</h3>
                                <p class="mb-5 text-sm text-gray-500 px-1">Confirm return of the purchase order</p>
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
                                label="PO Number:"
                                :value="returnFormData.poNumber"
                            />
                            <TextDisplay
                                label="Supplier:"
                                :value="returnFormData.supplierName"
                            />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-x-4">
                            <TextDisplay
                                label="Date Ordered:"
                                :value="formatDate(returnFormData.orderDate)"
                            />
                            <TextDisplay
                                label="Date Received:"
                                :value="formatDate(returnFormData.receivedDate)"
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
                                label="Received by:"
                                :value="returnFormData.receivedByName"
                            />
                        </div>

                        <hr class="mt-2 mb-5 mx-1">
                        
                        <div class="flex flex-col flex-1 overflow-hidden">
                            <div class="mb-5 flex items-center gap-2 text-lg">
                                <i class="fas fa-list"></i>
                                <span class="mr-2 font-medium">Ordered Items</span>
                            </div>
                            
                            <!-- Desktop table layout -->
                            <div class="relative flex-1 hidden overflow-hidden lg:flex">
                                
                                <!-- Gradient overlay -->
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>

                                <div class="flex-1 px-1 overflow-auto">
                                    <div class="grid grid-cols-[30%_15%_20%_20%_15%] head-row">
                                        <div class="head-data">Item</div>
                                        <div class="head-data">Cost</div>
                                        <div class="head-data">Ordered Qty</div>
                                        <div class="head-data">Received Qty</div>
                                        <div class="head-data">Received Subtotal</div>
                                    </div>
                                    
                                    <div v-for="item in returnFormData.selectedItemsList" class="grid grid-cols-[30%_15%_20%_20%_15%] body-row">
                                        <div class="grid body-data">
                                            <p class="item-name-style-wide" :title="item.itemName">
                                                {{ item.itemName }}
                                            </p>
                                            <p class="item-sku-style-wide" :title="item.sku">
                                                {{ item.sku }}
                                            </p>
                                        </div>
                                        <div class="body-data item-cost-style-wide">
                                            ₱{{ item.cost }}
                                        </div>
                                        <div class="body-data">
                                            <div class="text-gray-500 bg-gray-100 rounded-md h-fit">
                                                <p class="grid px-3  py-1.5 place-items-center">
                                                    {{ item.orderedQty + " " + (item.unit ?? (item.orderedQty < 2 ? 'unit' : 'units')) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="body-data">
                                            <div class="text-gray-500 bg-gray-100 rounded-md h-fit">
                                                <p class="grid px-3  py-1.5 place-items-center">
                                                    {{ item.receivedQty + " " + (item.unit ?? (item.receivedQty < 2 ? 'unit' : 'units')) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="body-data item-subtotal-style-wide">
                                            ₱{{ (item.cost * item.receivedQty).toFixed(2) }}
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
                                        <div v-for="item in returnFormData.selectedItemsList" class="cursor-default flex flex-col shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-lg max-h-full hover:shadow-[0_1px_4px_rgba(0,0,0,0.25)] transition-all">
                                        
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
                                                    <p class="label-style-two">Cost</p>
                                                    <p class="ml-auto item-cost-style-narrow">
                                                        ₱{{ item.cost }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="w-1/2 label-style-two">Ordered</p>
                                                    <p class="w-1/2 ml-auto px-3 py-1.5 text-sm text-gray-500 text-right bg-gray-100 rounded-md">
                                                        {{ item.orderedQty + " " + ( item.unit ?? (item.orderedQty < 2 ? 'unit' : 'units') ) }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="w-1/2 label-style-two">Received</p>
                                                    <p class="w-1/2 ml-auto px-3 py-1.5 text-sm text-gray-500 text-right bg-gray-100 rounded-md">
                                                        {{ item.receivedQty + " " + ( item.unit ?? (item.receivedQty < 2 ? 'unit' : 'units') ) }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center pt-2 border-t">
                                                    <p class="label-style-two">Received Subtotal</p>
                                                    <p class="ml-auto item-subtotal-style-narrow">
                                                        ₱{{ (item.cost * item.receivedQty).toFixed(2) }}
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

                            <ButtonWhite @click="showPOsList" width="full" type="button">
                                Cancel
                            </ButtonWhite>
                        </div>
                    </div>
                </form>
            </div>
        </transition>

        <!-- Update received form -->
        <transition
            :enter-from-class="formTransition.enterFrom"
            :enter-to-class="formTransition.enterTo"
            :enter-active-class="formTransition.enterActive"
            :leave-from-class="formTransition.leaveFrom"
            :leave-to-class="formTransition.leaveTo"
            :leave-active-class="formTransition.leaveActive"
            @after-leave="changeContent('Purchase Orders')"
        >
            <div v-show="viewingReceivedUpdateForm" class="flex flex-col w-full max-h-[1600px] min-h-[1600px] lg:max-h-[1300px] lg:min-h-[1200px] h-full max-w-[1300px] mx-auto ">
                <form @submit.prevent="updateReceivedPO" class="flex flex-col flex-1 w-full p-2 mx-auto overflow-hidden">
                    <section class="shadow-[0px_1px_4px_rgba(0,0,0,0.1)] overflow-hidden flex-1 p-6 sm:p-8 bg-white rounded-xl flex flex-col mb-4">
                        <div class="flex flex-row items-start">
                            <div class="flex flex-col">
                                <h3 class="font-bold text-left px-1">Update Purchase Order</h3>
                                <p class="mb-5 text-sm text-gray-500 px-1">Update purchase order details</p>
                            </div>
                            
                            <p class="px-3 py-1 ml-auto font-medium text-blue-600 bg-blue-100 rounded w-fit text-nowrap">
                                {{ "Status: " + receivedUpdateFormData.status }}
                            </p>
                        </div>

                        <hr class="mb-5 mx-1">

                        <div class="mb-5 flex items-center gap-2 text-lg">
                            <i class="fas fa-info-circle"></i>
                            <span class="mr-2 font-medium">Order Details</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-x-4">
                            <TextDisplay
                                label="PO Number:"
                                :value="receivedUpdateFormData.poNumber"
                            />
                            <TextDisplay
                                label="Supplier:"
                                :value="receivedUpdateFormData.supplierName"
                            />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-x-4">
                            <TextDisplay
                                label="Date Ordered:"
                                :value="formatDate(receivedUpdateFormData.orderDate)"
                            />
                            <TextDisplay
                                label="Date Received:"
                                :value="formatDate(receivedUpdateFormData.receivedDate)"
                            />
                        </div>

                        <TextArea
                            v-model="receivedUpdateFormData.remarks" 
                            id="remarks-update-received" 
                            label="Remarks:"
                        />

                        <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-x-4">
                            <TextDisplay
                                label="Created by:"
                                :value="receivedUpdateFormData.createdByName"
                            />

                            <TextDisplay
                                label="Received by:"
                                :value="receivedUpdateFormData.receivedByName"
                            />
                        </div>

                        <hr class="mt-2 mb-5 mx-1">
                        
                        <div class="flex flex-col flex-1 overflow-hidden">
                            <div class="mb-5 flex items-center gap-2 text-lg">
                                <i class="fas fa-list"></i>
                                <span class="mr-2 font-medium">Ordered Items</span>
                            </div>

                            <!-- Desktop table layout -->
                            <div class="relative flex-1 hidden overflow-hidden lg:flex">
                                
                                <!-- Gradient overlay -->
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                            
                                <div class="flex-1 px-1 overflow-auto ">
                                    <div class="grid grid-cols-[30%_15%_20%_20%_15%] head-row">
                                        <div class="head-data">Item</div>
                                        <div class="head-data">Cost</div>
                                        <div class="head-data">Ordered Qty</div>
                                        <div class="head-data">Received Qty</div>
                                        <div class="head-data">Received Subtotal</div>
                                    </div>
                                    
                                    <div v-for="item in receivedUpdateFormData.selectedItemsList" class="grid grid-cols-[30%_15%_20%_20%_15%] body-row">
                                        <div class="grid body-data">
                                            <p class="item-name-style-wide" :title="item.itemName">
                                                {{ item.itemName }}
                                            </p>
                                            <p class="item-sku-style-wide" :title="item.sku">
                                                {{ item.sku }}
                                            </p>
                                        </div>
                                        <div class="body-data item-cost-style-wide">
                                            ₱{{ item.cost }}
                                        </div>
                                        <div class="body-data">
                                            <div class="text-gray-500 bg-gray-100 rounded-md h-fit">
                                                <p class="grid px-3  py-1.5 place-items-center">
                                                    {{ item.orderedQty + " " + (item.unit ?? (item.orderedQty < 2 ? 'unit' : 'units')) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="body-data">
                                            <div class="flex flex-row w-full border border-gray-300 rounded-lg">
                                                <input 
                                                    v-model="item.receivedQty" 
                                                    type="number" 
                                                    class="flex-1 min-w-0 px-3 py-1.5 text-right text-sm bg-transparent focus:outline-none"
                                                >
                                                <span class="grid px-3 text-gray-500 border-l border-gray-300 whitespace-nowrap place-items-center">
                                                    {{ item.unit ?? (item.receivedQty < 2 ? 'unit' : 'units') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="body-data item-subtotal-style-wide">
                                            ₱{{ (item.cost * item.receivedQty).toFixed(2) }}
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
                                        <div v-for="item in receivedUpdateFormData.selectedItemsList" class="cursor-default flex flex-col shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-lg max-h-full hover:shadow-[0_1px_4px_rgba(0,0,0,0.25)] transition-all">
                                        
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
                                                    <p class="label-style-two">Cost</p>
                                                    <p class="ml-auto item-cost-style-narrow">
                                                        ₱{{ item.cost }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="label-style-two">Ordered</p>
                                                    <p class="w-1/2 ml-auto text-sm px-3 py-1.5  text-gray-500 text-right bg-gray-100 rounded-md">
                                                        {{ item.orderedQty + " " + ( item.unit ?? (item.orderedQty < 2 ? 'unit' : 'units') ) }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="w-1/2 label-style-two">Received</p>
                                                    <div class="flex items-center justify-between w-1/2 transition bg-white border border-gray-300 rounded-md">
                                                        <input 
                                                            v-model="item.receivedQty"
                                                            class="flex-1 min-w-0 px-1 py-1.5 text-right text-sm bg-transparent"
                                                            type="number"
                                                            placeholder="0"
                                                        >
                                                        <span class="px-3 text-sm text-gray-500 border-l border-gray-200 whitespace-nowrap">
                                                            {{ item.unit ?? (item.receivedQty < 2 ? 'unit' : 'units') }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center pt-2 border-t">
                                                    <p class="label-style-two">Received Subtotal</p>
                                                    <p class="ml-auto item-subtotal-style-narrow">
                                                        ₱{{ (item.cost * item.receivedQty).toFixed(2) }}
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

                            <ButtonWhite @click="showPOsList" width="full" type="button">
                                Cancel
                            </ButtonWhite>
                        </div>
                    </div>
                </form>
            </div>
        </transition>

        <!-- View PO details -->
        <transition
            :enter-from-class="formTransition.enterFrom"
            :enter-to-class="formTransition.enterTo"
            :enter-active-class="formTransition.enterActive"
            :leave-from-class="formTransition.leaveFrom"
            :leave-to-class="formTransition.leaveTo"
            :leave-active-class="formTransition.leaveActive"
            @after-leave="changeContent('Purchase Orders')"
        >
            <div v-show="viewingPODetails" class="flex flex-col w-full max-h-[1600px] min-h-[1600px] lg:max-h-[1300px] lg:min-h-[1200px] h-full max-w-[1300px] mx-auto ">
                <div class="flex flex-col flex-1 w-full p-2 mx-auto overflow-hidden">
                    <section class="shadow-[0px_1px_4px_rgba(0,0,0,0.1)] overflow-hidden flex-1 p-6 sm:p-8 bg-white rounded-xl flex flex-col mb-4">
                        <div class="flex flex-row items-start">
                            <div class="flex flex-col">
                                <h3 class="font-bold text-left px-1">Purchase Order Details</h3>
                                <p class="mb-5 text-sm text-gray-500 px-1">View purchase order information</p>
                            </div>
                            
                            <p 
                                :class="['px-3 py-1 w-fit font-medium rounded text-nowrap ml-auto', 
                                    poDetails.status == 'Pending' ? 'bg-yellow-100 text-yellow-600' : '', 
                                    poDetails.status == 'Received' ? 'bg-blue-100 text-blue-600' : '',
                                    poDetails.status == 'Cancelled' ? 'bg-red-100 text-red-600' : '', 
                                    poDetails.status == 'Returned' ? 'bg-orange-100 text-orange-600' : ''
                                ]"
                            >
                                {{ "Status: " + poDetails.status }}
                            </p>
                        </div>

                        <hr class="mb-5 mx-1">

                        <div class="mb-5 flex items-center gap-2 text-lg">
                            <i class="fas fa-info-circle"></i>
                            <span class="mr-2 font-medium">Order Details</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-x-4">
                            <TextDisplay
                                label="PO Number:"
                                :value="poDetails.poNumber"
                            />
                            <TextDisplay
                                label="Supplier:"
                                :value="poDetails.supplierName"
                            />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-x-4">
                            <TextDisplay
                                label="Date Ordered:"
                                :value="formatDate(poDetails.orderDate)"
                            />
                            <TextDisplay
                                label="Date Received:"
                                :value="poDetails.receivedDate ? formatDate(poDetails.receivedDate) : null"
                            />
                        </div>

                        <TextArea
                            v-model="poDetails.remarks" 
                            id="remarks-view" 
                            label="Remarks:"
                        />

                        <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-x-4">
                            <TextDisplay
                                label="Created by:"
                                :value="poDetails.createdByName"
                            />

                            <TextDisplay
                                label="Received by:"
                                :value="poDetails.receivedByName"
                            />
                        </div>

                        <hr class="mt-2 mb-5 mx-1">
                        
                        <div class="flex flex-col flex-1 overflow-hidden">
                            <div class="mb-5 flex items-center gap-2 text-lg">
                                <i class="fas fa-list"></i>
                                <span class="mr-2 font-medium">Ordered Items</span>
                            </div>

                            <!-- Desktop table layout -->
                            <div class="relative flex-1 hidden overflow-hidden lg:flex">
                                
                                <!-- Gradient overlay -->
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                            
                                <div class="flex-1 px-1 overflow-auto">
                                    <div class="grid grid-cols-[30%_15%_20%_20%_15%] head-row">
                                        <div class="head-data">Item</div>
                                        <div class="head-data">Cost</div>
                                        <div class="head-data">Ordered Qty</div>
                                        <div class="head-data">Received Qty</div>
                                        <div class="head-data">
                                            <span v-if="['Pending', 'Cancelled'].includes(poDetails.status)">
                                                Ordered Subtotal
                                            </span>
                                            <span v-else>
                                                Received Subtotal
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div v-for="item in poDetails.selectedItemsList" class="grid grid-cols-[30%_15%_20%_20%_15%] body-row">
                                        <div class="grid body-data">
                                            <p class="item-name-style-wide" :title="item.itemName">
                                                {{ item.itemName }}
                                            </p>
                                            <p class="item-sku-style-wide" :title="item.sku">
                                                {{ item.sku }}
                                            </p>
                                        </div>
                                        <div class="body-data item-cost-style-wide">
                                            ₱{{ item.cost }}
                                        </div>
                                        <div class="body-data">
                                            <div class="text-gray-500 bg-gray-100 rounded-md h-fit">
                                                <p class="grid px-3  py-1.5 place-items-center">
                                                    {{ item.orderedQty + " " + (item.unit ?? (item.orderedQty < 2 ? 'unit' : 'units')) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="body-data">
                                            <div class="text-gray-500 bg-gray-100 rounded-md h-fit">
                                                <p v-if="item.receivedQty" class="grid px-3  py-1.5 place-items-center">
                                                    {{ item.receivedQty + " " + (item.unit ?? (item.receivedQty < 2 ? 'unit' : 'units')) }}
                                                </p>
                                                <p v-else class="grid px-3  py-1.5 place-items-center">
                                                    -
                                                </p>
                                            </div>
                                        </div>
                                        <div class="body-data item-subtotal-style-wide">
                                            ₱{{ (item.cost * (item.receivedQty || item.orderedQty)).toFixed(2) }}
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
                                        <div v-for="item in poDetails.selectedItemsList" class="cursor-default flex flex-col shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-lg max-h-full hover:shadow-[0_1px_4px_rgba(0,0,0,0.25)] transition-all">
                                        
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
                                                    <p class="label-style-two">Cost</p>
                                                    <p class="ml-auto item-cost-style-narrow">
                                                        ₱{{ item.cost }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="w-1/2 label-style-two">Ordered</p>
                                                    <p class="w-1/2 ml-auto text-sm px-3 py-1.5  text-gray-500 text-right bg-gray-100 rounded-md">
                                                        {{ item.orderedQty + " " + ( item.unit ?? (item.orderedQty < 2 ? 'unit' : 'units') ) }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="w-1/2 label-style-two">Received</p>
                                                    <p v-if="item.receivedQty" class="w-1/2 ml-auto text-sm px-3 py-1.5  text-gray-500 text-right bg-gray-100 rounded-md h-fit">
                                                        {{ item.receivedQty + " " + ( item.unit ?? (item.receivedQty < 2 ? 'unit' : 'units') ) }}
                                                    </p>
                                                    <p v-else class="w-1/2 ml-auto text-sm px-3 py-1.5  text-gray-500 text-right bg-gray-100 rounded-md h-fit">
                                                        -
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center pt-2 border-t">
                                                    <p class="label-style-two">
                                                        <span v-if="['Pending', 'Cancelled'].includes(poDetails.status)">
                                                            Ordered Subtotal
                                                        </span>
                                                        <span v-else>
                                                            Received Subtotal
                                                        </span>
                                                    </p>
                                                    <p class="ml-auto item-subtotal-style-narrow">
                                                        ₱{{ (item.cost * (item.receivedQty || item.orderedQty)).toFixed(2) }}
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
                         <div class="flex flex-row justify-between w-full md:w-[200px]">
                            <ButtonWhite @click="showPOsList" width="full" type="button">
                                Back
                            </ButtonWhite>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>

    <Modal :inUse="viewingItemsListModal" @close="viewingItemsListModal = false">
        <div v-show="viewingItemsListModal" class="w-full mb-4 bg-white shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-xl h-full max-h-[500px] max-w-[1000px]">
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
                    
                    <!-- Seach bar -->
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
                                    <div class="head-data">Cost</div>
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
                                    <div class="body-data item-cost-style-wide">
                                        ₱{{ item.cost }}
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
                                            @click="toggleSelectItem(null, item.id, item.itemName, item.cost, item.unit, item.sku, item.category)" 
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
                                                    @click="toggleSelectItem(null, item.id, item.itemName, item.cost, item.unit, item.sku, item.category)"
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
                                                <p class="label-style-two">Cost</p>
                                                <p class="ml-auto item-cost-style-narrow">
                                                    ₱{{ item.cost }}
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
import { useMainContentTransition, useAlertLoadingTransition, useFormTransition } from '../composables/useTransition';
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
import ButtonYellow from '../components/buttons/ButtonYellow.vue'
import ButtonWhite from '../components/buttons/ButtonWhite.vue';
import TextInput from '../components/TextInput.vue';
import SelectDropdown from '../components/SelectDropdown.vue';
import TextArea from '../components/TextArea.vue';
import TextDisplay from '../components/TextDisplay.vue';

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

const viewingPOsList = ref(null)
const viewingReceiveForm = ref(null)
const viewingReceivedUpdateForm = ref(null)
const viewingPendingUpdateForm = ref(null)
const viewingCancelForm = ref(null)
const viewingReturnForm = ref(null)
const viewingPODetails = ref(null)
const viewingItemsListModal = ref(false)
const viewingSuppliersMenu = ref(false)
const pageContent = ref(null)

const searchPOValue = ref("")
const searchItemValue = ref("")
const currentPOId = ref(null)
const loading = ref(false)

const selectedItemIds = ref([])
const suppliersListData = ref([])

const posListData = ref([])
const dataStatus = ref(null)
const filteredPOsListData = computed(() => {
    return posListData.value.filter(po => {
        return po.status.toLowerCase().includes(searchPOValue.value.toLowerCase()) ||
            po.poNumber.toLowerCase().includes(searchPOValue.value.toLowerCase()) 
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

const receivingFormData = ref({
    userId: null,
    userName: null,
    hdrId: null,
    poNumber: null,
    remarks: null,
    orderDate: null,
    status: null,
    createdByName: null,
    supplierId: null,
    supplierName: null,
    selectedItemsList: ref([])
})

const receivedUpdateFormData = ref({
    hdrId: null,
    poNumber: null,
    remarks: null,
    orderDate: null,
    receivedDate: null,
    status: null,
    createdByName: null,
    receivedByName: null,
    supplierId: null,
    supplierName: null,
    selectedItemsList: ref([])
})

const pendingUpdateFormData = ref({
    hdrId: null,
    poNumber: null,
    remarks: null,
    orderDate: null,
    status: null,
    createdByName: null,
    supplierId: null,
    supplierName: null,
    selectedItemsList: ref([]),
    removedDtlIds: ref([])
})

const cancelFormData = ref({
    hdrId: null,
    poNumber: null,
    remarks: null,
    orderDate: null,
    status: null,
    createdByName: null,
    supplierId: null,
    supplierName: null,
    selectedItemsList: ref([])
})

const returnFormData = ref({
    hdrId: null,
    poNumber: null,
    remarks: null,
    orderDate: null,
    receivedDate: null,
    status: null,
    createdByName: null,
    receivedByName: null,
    supplierId: null,
    supplierName: null,
    selectedItemsList: ref([])
})

const poDetails = ref({
    hdrId: null,
    poNumber: null,
    remarks: null,
    orderDate: null,
    receivedDate: null,
    status: null,
    createdByName: null,
    receivedByName: null,
    supplierId: null,
    supplierName: null,
    selectedItemsList: ref([])
})

async function getPOsListData() {
    dataStatus.value = "Loading..."

    try {
        const response = await axiosClient.get('/api/pos-list')

        posListData.value = response.data.posListData
        suppliersListData.value = response.data.suppliersListData
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to load purchase orders list data')
    }
    finally{
        if(posListData.value.length < 1){
            dataStatus.value = "No data"
        }else{
            dataStatus.value = null
        }          
    }
}

async function receivePO(){
    loading.value = true

    try {
        const response = await axiosClient.put('/api/receive-po', receivingFormData.value)
        
        showSuccessMsg('Success', response.data.message)
        await getPOsListData()
        showPOsList() 
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to receive purchase order')  
    }
    finally{
        loading.value = false
    }
}

async function updateReceivedPO(){
    loading.value = true

    try {
        const response = await axiosClient.put('/api/update-received-po', receivedUpdateFormData.value)
        
        showSuccessMsg('Success', response.data.message)
        await getPOsListData()
        showPOsList() 
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to update purchase order')  
    }
    finally{
        loading.value = false
    }
}

async function updatePendingPO () {
    loading.value = true

    try {
        const response = await axiosClient.put('/api/update-pending-po', pendingUpdateFormData.value)
        
        showSuccessMsg('Success', response.data.message)
        await getPOsListData()
        showPOsList()  
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to update purchase order')
    }
    finally{
        loading.value = false
    }
}

async function cancelPO(){
    loading.value = true

    try {
        const response = await axiosClient.put('/api/cancel-po', cancelFormData.value)

        showSuccessMsg('Success', response.data.message)
        await getPOsListData()
        showPOsList()  
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to cancel purchase order')
    }
    finally{
        loading.value = false
    }
}

async function returnPO(){
    loading.value = true

    try {
        const response = await axiosClient.put('/api/return-po', returnFormData.value)
        
        showSuccessMsg('Success', response.data.message)
        await getPOsListData()
        showPOsList()  
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to return purchase order')
    }
    finally{
        loading.value = false
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

    } catch (error) {
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

async function showReceivingForm(po) {
    loading.value = true
    receivingFormData.value.hdrId = po.hdrId
    receivingFormData.value.poNumber = po.poNumber
    receivingFormData.value.orderDate = po.orderDate
    receivingFormData.value.remarks = po.remarks
    receivingFormData.value.status = po.status
    receivingFormData.value.createdByName = po.createdByName
    receivingFormData.value.supplierId = po.supplierId
    receivingFormData.value.supplierName = po.supplierName
    
    try {
        const response = await axiosClient.get('/api/po-dtls-received', {params: { hdrId: po.hdrId }})
        
        receivingFormData.value.selectedItemsList = response.data.po_details
            
        viewingPOsList.value = false
        pageContent.value = 'Receive Purchase Order'
        
        receivingFormData.value.selectedItemsList.forEach(item => {
            item.receivedQty = item.orderedQty
        });
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to fetch purchase order details')
    }
    finally{
        loading.value = false
    }
}

async function showReceivedUpdateForm(po) {
    loading.value = true
    receivedUpdateFormData.value.hdrId = po.hdrId
    receivedUpdateFormData.value.poNumber = po.poNumber
    receivedUpdateFormData.value.orderDate = po.orderDate
    receivedUpdateFormData.value.remarks = po.remarks
    receivedUpdateFormData.value.status = po.status
    receivedUpdateFormData.value.createdByName = po.createdByName
    receivedUpdateFormData.value.receivedByName = po.receivedByName
    receivedUpdateFormData.value.receivedDate = po.receivedDate
    receivedUpdateFormData.value.supplierId = po.supplierId
    receivedUpdateFormData.value.supplierName = po.supplierName

    try {
        const response = await axiosClient.get('/api/po-dtls-received', {params: { hdrId: po.hdrId }})

        receivedUpdateFormData.value.selectedItemsList = response.data.po_details
            
        viewingPOsList.value = false
        pageContent.value = 'Update Received PO'
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to fetch purchase order details')
    }
    finally{
        loading.value = false
    }
}

async function showPendingUpdateForm(po) {
    loading.value = true
    pendingUpdateFormData.value.hdrId = po.hdrId
    pendingUpdateFormData.value.poNumber = po.poNumber
    pendingUpdateFormData.value.orderDate = po.orderDate
    pendingUpdateFormData.value.remarks = po.remarks
    pendingUpdateFormData.value.status = po.status
    pendingUpdateFormData.value.createdByName = po.createdByName
    pendingUpdateFormData.value.supplierId = po.supplierId
    pendingUpdateFormData.value.supplierName = po.supplierName

    try {
        const response = await axiosClient.get('/api/po-dtls-pending', {params: { hdrId: po.hdrId }})

        pendingUpdateFormData.value.selectedItemsList = response.data.po_details
        pendingUpdateFormData.value.selectedItemsList.forEach(item => {
            selectedItemIds.value.push(item.itemId)
        })

        viewingPOsList.value = false
        pageContent.value = 'Update Pending PO'
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to fetch purchase order details')
    }
    finally{
        loading.value = false
    }
}

async function showPODetails(po){
    loading.value = true
    poDetails.value.hdrId = po.hdrId
    poDetails.value.poNumber = po.poNumber
    poDetails.value.remarks = po.remarks
    poDetails.value.orderDate = po.orderDate
    poDetails.value.receivedDate = po.receivedDate
    poDetails.value.status = po.status
    poDetails.value.createdByName = po.createdByName
    poDetails.value.supplierId = po.supplierId
    poDetails.value.supplierName = po.supplierName
    poDetails.value.receivedByName = po.receivedByName

    try {
        const response = await axiosClient.get('/api/po-dtls-received', {params: { hdrId: po.hdrId }})
        
        poDetails.value.selectedItemsList = response.data.po_details

        viewingPOsList.value = false
        pageContent.value = 'View PO Details'
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to fetch purchase order details')
    }
    finally{
        loading.value = false
    }
}

async function showCancelForm(po){
    loading.value = true
    cancelFormData.value.hdrId = po.hdrId
    cancelFormData.value.poNumber = po.poNumber
    cancelFormData.value.orderDate = po.orderDate
    cancelFormData.value.remarks = po.remarks
    cancelFormData.value.status = po.status
    cancelFormData.value.createdByName = po.createdByName
    cancelFormData.value.supplierId = po.supplierId
    cancelFormData.value.supplierName = po.supplierName

    try {
        const response = await axiosClient.get('/api/po-dtls-pending', {params: { hdrId: po.hdrId }})
        
        cancelFormData.value.selectedItemsList = response.data.po_details

        viewingPOsList.value = false
        pageContent.value = 'Cancel PO'
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to fetch purchase order details')
    }
    finally{
        loading.value = false
    }
}

async function showReturnForm(po){
    loading.value = true
    returnFormData.value.hdrId = po.hdrId
    returnFormData.value.poNumber = po.poNumber
    returnFormData.value.remarks = po.remarks
    returnFormData.value.orderDate = po.orderDate
    returnFormData.value.receivedDate = po.receivedDate
    returnFormData.value.status = po.status
    returnFormData.value.createdByName = po.createdByName
    returnFormData.value.receivedByName = po.receivedByName
    returnFormData.value.supplierId = po.supplierId
    returnFormData.value.supplierName = po.supplierName

    try {
        const response = await axiosClient.get('/api/po-dtls-received', {params: { hdrId: po.hdrId }})

        returnFormData.value.selectedItemsList = response.data.po_details
            
        viewingPOsList.value = false
        pageContent.value = 'Return PO'
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to fetch purchase order details')
    }
    finally{
        loading.value = false
    }
}


function changeContent(content){
    if(content === 'Purchase Orders'){
        viewingPOsList.value = true
    }else if(content === 'Update Pending PO'){
        viewingPendingUpdateForm.value = true
    }else if(content === 'Receive Purchase Order'){
        viewingReceiveForm.value = true
    }else if(content === 'Update Received PO'){
        viewingReceivedUpdateForm.value = true
    }else if(content === 'Cancel PO'){
        viewingCancelForm.value = true
    }else if(content === 'Return PO'){
        viewingReturnForm.value = true
    }else if(content === 'View PO Details'){
        viewingPODetails.value = true
    }else{
        return
    }

    mainContentTransition.value.enterFrom = "-translate-x-1/2 opacity-0"
}

function showPOsList() {
    viewingReceiveForm.value = false
    viewingReceivedUpdateForm.value = false
    viewingPendingUpdateForm.value = false
    viewingCancelForm.value = false
    viewingReturnForm.value = false
    viewingPODetails.value = false

    receivingFormData.value.hdrId = null
    receivingFormData.value.remarks = null
    receivingFormData.value.orderDate = null
    receivingFormData.value.status = null
    receivingFormData.value.createdByName = null
    receivingFormData.value.supplierId = null
    receivingFormData.value.supplierName = null
    receivingFormData.value.selectedItemsList = []    

    receivedUpdateFormData.value.hdrId = null
    receivedUpdateFormData.value.remarks = null
    receivedUpdateFormData.value.orderDate = null
    receivedUpdateFormData.value.receivedDate = null
    receivedUpdateFormData.value.status = null
    receivedUpdateFormData.value.createdByName = null
    receivedUpdateFormData.value.receivedByName = null
    receivedUpdateFormData.value.supplierId = null
    receivedUpdateFormData.value.supplierName = null
    receivedUpdateFormData.value.selectedItemsList = []   
    
    pendingUpdateFormData.value.hdrId = null
    pendingUpdateFormData.value.remarks = null
    pendingUpdateFormData.value.orderDate = null
    pendingUpdateFormData.value.status = null
    pendingUpdateFormData.value.createdByName = null
    pendingUpdateFormData.value.supplierId = null
    pendingUpdateFormData.value.supplierName = null
    pendingUpdateFormData.value.selectedItemsList = []  
    pendingUpdateFormData.value.removedDtlIds = []

    cancelFormData.value.hdrId = null
    cancelFormData.value.remarks = null
    cancelFormData.value.orderDate = null
    cancelFormData.value.status = null
    cancelFormData.value.createdByName = null
    cancelFormData.value.supplierId = null
    cancelFormData.value.supplierName = null
    cancelFormData.value.selectedItemsList = []  

    returnFormData.value.hdrId = null
    returnFormData.value.remarks = null
    returnFormData.value.orderDate = null
    returnFormData.value.receivedDate = null
    returnFormData.value.status = null
    returnFormData.value.createdByName = null
    returnFormData.value.receivedByName = null
    returnFormData.value.supplierId = null
    returnFormData.value.supplierName = null
    returnFormData.value.selectedItemsList = [] 

    poDetails.value.hdrId = null
    poDetails.value.remarks = null
    poDetails.value.orderDate = null
    poDetails.value.receivedDate = null
    poDetails.value.status = null
    poDetails.value.createdByName = null
    poDetails.value.receivedByName = null
    poDetails.value.supplierId = null
    poDetails.value.supplierName = null
    poDetails.value.selectedItemsList = [] 
    
    selectedItemIds.value = []
}

function toggleMoreActions (id){
    if (currentPOId.value == id){
        currentPOId.value = null
    }else{
        currentPOId.value = id
    } 
}

function toggleSelectItem(dtlId, itemId, itemName, itemCost, itemUnit, itemSKU, itemCategory){

    if (selectedItemIds.value.includes(itemId)){

        // Add to pendingUpdateFormData.removedDtlIds
        pendingUpdateFormData.value.selectedItemsList.forEach(selectedItem => {
            if(selectedItem.itemId === itemId){
                if(selectedItem.dtlId){
                    pendingUpdateFormData.value.removedDtlIds.push(selectedItem.dtlId)
                }
            }
        })

        // Remove from pendingUpdateFormData.selectedItemsList
        pendingUpdateFormData.value.selectedItemsList = pendingUpdateFormData.value.selectedItemsList.filter(item => {
            return item.itemId != itemId
        });

        // Remove from selectedItemIds
        selectedItemIds.value = selectedItemIds.value.filter(i => {
            return i != itemId
        })        
    }
    else{
        pendingUpdateFormData.value.selectedItemsList.push({
            dtlId: null,
            itemId: itemId, 
            itemName: itemName, 
            sku: itemSKU,
            unit: itemUnit,
            cost: itemCost,
            orderedQty: 1,
            category: itemCategory,
        })

        selectedItemIds.value.push(itemId)
    }
}

function showItemsListModal(){
    viewingItemsListModal.value = true
    getItemsListData()
}

function selectSupplier(supplier){
    if(supplier){
        pendingUpdateFormData.value.supplierId = supplier.id
        pendingUpdateFormData.value.supplierName = supplier.name
    }else{
        pendingUpdateFormData.value.supplierId = null
        pendingUpdateFormData.value.supplierName = null
    }
}

function handleClickOutside(e) {
    if (!e.target.closest(".btnActions")) {
        currentPOId.value = null
    }
    
    if(!e.target.closest('.suppliers-dropdown')){
        viewingSuppliersMenu.value = false
    }
}

onMounted(() => {
    document.addEventListener("click", handleClickOutside)

    getPOsListData()
    viewingPOsList.value = true

    receivingFormData.value.userId = user.value.id
    receivingFormData.value.userName = user.value.name
})

onBeforeUnmount(() => {
    document.removeEventListener("click", handleClickOutside)
})
</script>

<style scoped>

</style>