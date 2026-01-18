<template>
  
  <div class="relative h-full w-full min-h-fit min-w-[200px]">
     
    <!-- Items list -->
    <transition
      :enter-from-class="mainContentTransition.enterFrom "
      :enter-to-class="mainContentTransition.enterTo"
      :enter-active-class="mainContentTransition.enterActive"
      :leave-from-class="mainContentTransition.leaveFrom"
      :leave-to-class="mainContentTransition.leaveTo"
      :leave-active-class="mainContentTransition.leaveActive"
      @after-leave="changeContent(pageContent)"
    >
      <section v-show="viewingItemsList" class="h-full flex flex-col max-h-[1200px] min-h-[750px] lg:min-h-[500px] w-full max-w-[1300px] mx-auto">
        <div class="flex flex-row items-start justify-between px-2 mt-2 mb-4">
          <h3 class="font-semibold tracking-wider">Items List</h3>

          <ButtonYellow @click="showAddForm" type="button" class="sm:order-2">
            <div class="relative flex flex-row items-center w-fit ">
              <span class="text-sm px-2">
                  <i class="fa-solid fa-plus"></i>
              </span>
              <span class="pr-4">New Item</span>
            </div>
          </ButtonYellow>
        </div>
        
        <div class="flex flex-1 p-2 overflow-hidden">
          <div class="flex flex-col flex-1 overflow-hidden min-h-[350px] bg-white rounded-xl shadow-[0px_1px_4px_rgba(0,0,0,0.1)] p-6">

            <!-- Search bar -->
            <div class="flex mb-4">
              <input 
                v-model="searchValue" 
                type="text" 
                placeholder="Search by item name or SKU" 
                class="search-bar"
              >
            </div>

            <div class="flex flex-col flex-1 pb-2 overflow-hidden">

              <!-- Desktop table layout -->
              <div class="relative flex-1 hidden overflow-hidden lg:flex">

                <!-- Gradient overlay -->
                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                
                <div class="flex-1 px-1 overflow-auto">
                  <div class="grid grid-cols-[25%_20%_15%_15%_15%_10%] head-row">
                    <div class="head-data">Item</div>
                    <div class="head-data">Category</div>
                    <div class="head-data">Cost</div>
                    <div class="head-data">Price</div>
                    <div class="head-data">Quantity</div>
                    <div class="justify-center head-data">Action</div>
                  </div>

                  <div v-if="dataStatus" class="flex items-center justify-center w-full py-3">
                    <p>{{ dataStatus }}</p>
                  </div>

                  <div v-if="!dataStatus" v-for="item in filteredItemsListData" class="grid grid-cols-[25%_20%_15%_15%_15%_10%] body-row">
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
                    <div class="body-data item-price-style-wide">
                      ₱{{ item.price }}
                    </div>
                    <div class="body-data">
                      <div class="item-quantity-style-wide" :class="getStockColor(item)">
                        {{ item.quantity + " " + (item.unit ?? (item.quantity < 2 ? 'unit' : 'units')) }}
                      </div>
                    </div>
                    <div class="flex justify-center items-center body-data">
                      <button 
                        @click="toggleMoreActions(item.id)" 
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
                        <div v-if="currentItemId == item.id" class="absolute inset-0 flex items-center justify-end w-full h-full">
                          <div class="bg-gradient-to-r flex-1 h-full from-[rgba(0,0,0,0.01)] to-darkgray-pri "></div>

                          <div class="flex items-center justify-end h-full p-2 bg-darkgray-pri w-fit btnActions">
                            <button 
                              @click="showUpdateForm(item)" 
                              class="h-full px-4 font-semibold text-white rounded-md w-fit hover:bg-darkgray-sec"
                              type="button"
                            >
                              Update
                            </button>

                            <button 
                              @click="openConfirmModal('Delete Item', 'Are you sure you want to delete this item? This item will no longer be usable, but existing transactions will remain unchanged.', item.id)"
                              class="h-full px-4 font-semibold text-white rounded-md w-fit hover:bg-red-700 "
                              type="button" 
                            >
                              Delete
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
                      v-for="item in filteredItemsListData" 
                      class="relative cursor-default flex flex-col rounded-lg shadow-[0px_1px_4px_rgba(0,0,0,0.1)] max-h-full hover:shadow-[0_1px_4px_rgba(0,0,0,0.25)] transition-all"
                    >
                      
                      <!-- Card head -->
                      <div class="flex items-center justify-between pt-5 pb-3 mx-5 mb-2 border-b border-gray-200">
                        <div class="overflow-hidden">
                          <h5 class="item-name-style-narrow" :title="item.itemName">
                            {{ item.itemName }}
                          </h5>
                          <p class="item-sku-style-narrow"  :title="item.sku">
                            {{ item.sku ?? 'Not set' }}
                          </p>
                        </div>
                        <button 
                          @click="toggleMoreActions(item.id)" 
                          class="px-4 py-1 ml-auto rounded-md btnActions hover:bg-gray-300"
                          type="button"
                        >
                          <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>
                      </div>

                      <!-- Card body -->
                      <div class="grid gap-2 px-5 pt-2 pb-5 ">

                        <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                          <p class="label-style-two">Quantity</p>
                          <span :class="getStockColor(item)" class="item-quantity-style-narrow ml-auto">
                            {{ item.quantity + ' ' + (item.unit ?? ( item.quantity < 2 ? 'unit' : 'units') ) }}
                          </span>
                        </div>

                        <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                          <p class="label-style-two">Cost</p>
                          <p class="ml-auto item-cost-style-narrow">
                            ₱{{ item.cost }}
                          </p>
                        </div>
                        
                        <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                          <p class="label-style-two">Price</p>
                          <p class="ml-auto item-price-style-narrow">
                            ₱{{ item.price }}
                          </p>
                        </div>
                        
                        <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                          <p class="label-style-two">Category</p>
                          <p class="ml-auto item-category-style-narrow" :title="item.category">
                            {{ item.category ?? 'Not Set' }}
                          </p>
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
                        <div v-if="currentItemId == item.id" class="absolute flex flex-col items-center justify-center w-full h-full gap-1 rounded-lg bg-black/60">
                          <div class="grid w-3/5 grid-cols-1 gap-1 btnActions min-w-[150px]">
                            <button 
                              @click="showUpdateForm(item)" 
                              class="w-full px-4 py-2 bg-white rounded-lg hover:shadow-[0_0_6px] hover:shadow-white/70" 
                              type="button"
                            >
                              Update
                            </button>

                            <button 
                              @click="openConfirmModal('Delete Item', 'Are you sure you want to delete this item? This item will no longer be usable, but existing transactions will remain unchanged.', item.id)"
                              class="w-full px-4 py-2 bg-white text-red-700 rounded-lg hover:shadow-[0_0_6px] hover:shadow-red-500/90"
                              type="button" 
                            >
                              Delete
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
    
    <!-- Create form -->
    <transition
      :enter-from-class="formTransition.enterFrom"
      :enter-to-class="formTransition.enterTo"
      :enter-active-class="formTransition.enterActive"
      :leave-from-class="formTransition.leaveFrom"
      :leave-to-class="formTransition.leaveTo"
      :leave-active-class="formTransition.leaveActive"
      @after-leave="changeContent('Items List')"
    >
      <section v-show="viewingAddForm" class="w-full">
        <form @submit.prevent="submitAddForm" class="w-full max-w-lg p-6 mx-auto mb-4 bg-white shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-xl">
          <h2 class="mb-10 text-2xl font-bold text-center">New Item</h2>

          <div class="flex flex-col w-full h-[76px]">
              <label for="add-item" class="text-sm label-style-one">Item Name:</label>
              <input 
                v-model="addFormData.itemName" 
                id="add-item" 
                class="value-style"
                type="text" 
              >
              <p class="block ml-2 text-sm text-red-500 h-[20px]">
                {{ errors.itemName ? errors.itemName[0] : '' }}
              </p>
          </div>

          <div class="flex flex-col w-full h-[76px]">
            <label for="add-description" class="text-sm label-style-one">Description:</label>
            <input 
              v-model="addFormData.description" 
              id="add-description" 
              type="text" 
              class="value-style"
            >
          </div>

          <div class="flex flex-col w-full h-[76px]">
            <p class="text-sm label-style-one">Category:</p>
            <div @click="viewingCategoryMenu = !viewingCategoryMenu" class="category-dropdown relative cursor-pointer value-style">
              <div class="flex">
                <p v-if="addFormData.categoryName">
                  {{ addFormData.categoryName }}
                </p>
                <p v-else class="text-gray-400">
                  None
                </p>
                <span class="ml-auto">
                  <i class="transition-all transform fa-solid fa-caret-down" :class="viewingCategoryMenu ? '-rotate-180 ' : ''"></i>
                </span>
              </div>

              <Transition
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                enter-active-class="transition duration-200 transform"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
                leave-active-class="duration-200 transform transtion"
              >
                <div v-if="viewingCategoryMenu" class="z-20 p-3 absolute flex-col flex w-full bg-white rounded-md shadow-[0px_1px_4px_rgba(0,0,0,0.1)] translate-y-3 -translate-x-3 overflow-auto">
                  <ul class="max-h-40 overflow-auto">
                    <li @click="selectCategory()" class="p-2 hover:bg-gray-50 cursor-pointer">
                      None
                    </li>

                    <li
                      v-for="category in categoriesListData"
                      @click="selectCategory(category.id, category.categoryName)"
                      class="p-2 hover:bg-gray-100 cursor-pointer"
                    >
                      {{ category.categoryName }}
                    </li>
                  </ul>
                </div>
              </Transition>
            </div>
          </div>

          <div class="flex flex-wrap w-full gap-x-4">
            <div class="flex flex-col flex-1 min-w-[80px] h-[76px]">
              <label for="add-cost" class="text-sm label-style-one">Cost:</label>
              <input 
                v-model="addFormData.cost" 
                id="add-cost" 
                step="0.01" 
                min="0" 
                type="number" 
                class="value-style"
              >
              <p class="block ml-2 text-sm text-red-500 h-[20px]">
                {{ errors.cost ? errors.cost[0] : '' }}
              </p>
            </div>

            <div class="flex flex-col flex-1 min-w-[80px] h-[76px]">
              <label for="add-price" class="text-sm label-style-one">Price:</label>
              <input 
                v-model="addFormData.price" 
                id="add-price" 
                step="0.01" 
                min="0" 
                type="number" 
                class="value-style"
              >
              <p class="block ml-2 text-sm text-red-500 h-[20px]">
                {{ errors.price ? errors.price[0] : '' }}
              </p>
            </div>

            <div class="flex flex-col flex-1 min-w-[150px] h-[76px]">
              <p class="text-sm label-style-one">Unit Measurement:</p>
              <div @click="viewingUnitMenu = !viewingUnitMenu" class="unit-dropdown relative cursor-pointer value-style">
                <div class="flex">
                  <p v-if="addFormData.unitName">
                    {{ addFormData.unitName }}
                  </p>
                  <p v-else class="text-gray-400">
                    None
                  </p>
                  <span class="ml-auto">
                    <i class="transition-all transform fa-solid fa-caret-down" :class="viewingUnitMenu ? '-rotate-180 ' : ''"></i>
                  </span>
                </div>

                <Transition
                  enter-from-class="opacity-0"
                  enter-to-class="opacity-100"
                  enter-active-class="transition duration-200 transform"
                  leave-from-class="opacity-100"
                  leave-to-class="opacity-0"
                  leave-active-class="duration-200 transform transtion"
                >
                  <div v-if="viewingUnitMenu" class="z-20 p-3 absolute flex-col flex w-full bg-white rounded-md shadow-[0px_1px_4px_rgba(0,0,0,0.1)] translate-y-3 -translate-x-3 overflow-auto">
                    <ul class="max-h-32 overflow-auto">
                      <li @click="selectUnit()" class="p-2 hover:bg-gray-50 cursor-pointer">
                        None
                      </li>

                      <li
                        v-for="unit in unitsListData"
                        @click="selectUnit(unit.id, unit.unitName)"
                        class="p-2 hover:bg-gray-50 cursor-pointer"
                      >
                        {{ unit.unitName }}
                      </li>
                    </ul>
                  </div>
                </Transition>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap w-full mb-4 gap-x-4">
            <div class="flex flex-col flex-1 min-w-[120px] h-[76px]">
              <label for="add-sku" class="text-sm label-style-one">SKU:</label>
              <input 
                v-model="addFormData.sku" 
                id="add-sku" 
                type="text" 
                class="value-style"
              >
            </div>

            <div class="flex flex-col flex-1 min-w-[120px] h-[76px]">
              <label for="add-reorder" class="text-sm label-style-one">Reorder level:</label>
              <input 
                v-model="addFormData.reorderLevel" 
                id="add-reorder" 
                type="number" 
                class="value-style"
              >
              <p class="block ml-2 text-sm text-red-500 h-[20px]">
                {{ errors.reorderLevel ? errors.reorderLevel[0] : '' }}
              </p>
            </div> 
          </div>
          
          <div class="flex items-end justify-end w-full">
            <div class="grid grid-cols-2 w-full gap-2 lg:gap-4">
              <ButtonYellow type="submit" width="full">
                Save
              </ButtonYellow>

              <ButtonWhite @click="showItemsList" type="button" width="full">
                Cancel
              </ButtonWhite>
            </div>
          </div>
        </form>
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
      @after-leave="changeContent('Items List')"
    >
      <section v-show="viewingUpdateForm" class="w-full ">
        <form @submit.prevent="submitUpdateForm(updateFormData.id)" class="w-full max-w-lg p-6 mx-auto mb-4 bg-white shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-xl">
          <h2 class="mb-10 text-2xl font-bold text-center">Update Item</h2>

          <div class="flex flex-col w-full h-[76px]">
              <label for="update-item" class="text-sm label-style-one">Item Name:</label>
              <input 
                v-model="updateFormData.itemName" 
                id="update-item" 
                type="text" 
                class="value-style"
              >
              <p class="block ml-2 text-sm text-red-500 h-[20px]">
                {{ errors.itemName ? errors.itemName[0] : '' }}
              </p>
          </div>
          
          <div class="flex flex-col w-full h-[76px]">
            <label for="update-description" class="text-sm label-style-one">Description:</label>
            <input 
              v-model="updateFormData.description"
              id="update-description" 
              type="text"
              class="value-style"
            >
          </div>

          <div class="flex flex-col w-full h-[76px]">
            <p class="text-sm label-style-one">Category:</p>
            <div @click="viewingCategoryMenu = !viewingCategoryMenu" class="category-dropdown relative cursor-pointer value-style">
              <div class="flex">
                <p v-if="updateFormData.categoryName">
                  {{ updateFormData.categoryName }}
                </p>
                <p v-else class="text-gray-400 ">
                  None
                </p>
                <span class="ml-auto">
                  <i class="transition-all transform fa-solid fa-caret-down" :class="viewingCategoryMenu ? '-rotate-180 ' : ''"></i>
                </span>
              </div>
              
              <Transition
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                enter-active-class="transition duration-200 transform"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
                leave-active-class="duration-200 transform transtion"
              >
                <div v-if="viewingCategoryMenu" class="z-20 p-3 absolute flex-col flex w-full bg-white rounded-md shadow-[0px_1px_4px_rgba(0,0,0,0.1)] translate-y-3 -translate-x-3 overflow-auto">
                  <ul class="max-h-40 overflow-auto">
                    <li @click="selectCategory()" class="p-2 hover:bg-gray-50 cursor-pointer">
                      None
                    </li>

                    <li
                      v-for="category in categoriesListData"
                      @click="selectCategory(category.id, category.categoryName)"
                      class="p-2 hover:bg-gray-50 cursor-pointer"
                    >
                      {{ category.categoryName }}
                    </li>
                  </ul>
                </div>
              </Transition>
            </div>
          </div>
          
          <div class="flex flex-wrap w-full gap-x-4">
            <div class="flex flex-col flex-1 min-w-[80px] h-[76px]">
              <label for="update-cost" class="text-sm label-style-one">Cost:</label>
              <input 
                v-model="updateFormData.cost" 
                id="update-cost" 
                step="0.01" 
                min="0" 
                type="number" 
                class="value-style"
              >
              <p class="block ml-2 text-sm text-red-500 h-[20px]">
                {{ errors.cost ? errors.cost[0] : '' }}
              </p>
            </div>

            <div class="flex flex-col flex-1 min-w-[80px] h-[76px]">
              <label for="update-price" class="text-sm label-style-one">Price:</label>
              <input 
                v-model="updateFormData.price" 
                id="update-price" 
                step="0.01" 
                min="0" 
                type="number" 
                class="value-style"
              >
              <p class="block ml-2 text-sm text-red-500 h-[20px]">
                {{ errors.price ? errors.price[0] : '' }}
              </p>
            </div>

            <div class="flex flex-col flex-1 min-w-[150px] h-[76px]">
              <label class="text-sm label-style-one">Unit Measurement:</label>
              <div @click="viewingUnitMenu = !viewingUnitMenu" class="unit-dropdown relative cursor-pointer value-style">
                <div class="flex">
                  <p v-if="updateFormData.unitName">
                    {{ updateFormData.unitName }}
                  </p>
                  <p v-else class="text-gray-400 ">
                    None
                  </p>
                  <span class="ml-auto">
                    <i class="transition-all transform fa-solid fa-caret-down" :class="viewingUnitMenu ? '-rotate-180 ' : ''"></i>
                  </span>
                </div>

                <Transition
                  enter-from-class="opacity-0"
                  enter-to-class="opacity-100"
                  enter-active-class="transition duration-200 transform"
                  leave-from-class="opacity-100"
                  leave-to-class="opacity-0"
                  leave-active-class="duration-200 transform transtion"
                >
                  <div v-if="viewingUnitMenu" class="z-20 p-3 absolute flex-col flex w-full bg-white rounded-md shadow-[0px_1px_4px_rgba(0,0,0,0.1)] translate-y-3 -translate-x-3 overflow-auto">
                    <ul class="max-h-32 overflow-auto">
                      <li @click="selectUnit()" class="p-2 hover:bg-gray-50 cursor-pointer">
                        None
                      </li>

                      <li
                        v-for="unit in unitsListData"
                        @click="selectUnit(unit.id, unit.unitName)"
                        class="p-2 hover:bg-gray-50 cursor-pointer"
                      >
                        {{ unit.unitName }}
                      </li>
                    </ul>
                  </div>
                </Transition>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap w-full mb-4 gap-x-4">
            <div class="flex flex-col flex-1 min-w-[120px] h-[76px]">
              <label for="update-sku" class="text-sm label-style-one">SKU:</label>
              <input 
                v-model="updateFormData.sku" 
                id="update-sku" 
                type="text" 
                class="value-style"
              >
            </div>

            <div class="flex flex-col flex-1 min-w-[120px] h-[76px]">
              <label for="update-reorder" class="text-sm label-style-one">Reorder level:</label>
              <input 
                v-model="updateFormData.reorderLevel" 
                id="update-reorder" 
                type="number" 
                class="value-style"
              >
              <p class="block ml-2 text-sm text-red-500 h-[20px]">
                {{ errors.reorderLevel ? errors.reorderLevel[0] : '' }}
              </p>
            </div>
          </div>
          
          <div class="flex items-end justify-end w-full">
            <div class="grid grid-cols-2 w-full gap-2 lg:gap-4">
              <ButtonYellow type="submit" width="full">
                Save
              </ButtonYellow>
              
              <ButtonWhite @click="showItemsList" type="button" width="full">
                Cancel
              </ButtonWhite>
            </div>
          </div>
        </form>
      </section>
    </transition>
  </div>

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

    <ConfirmModal 
      :inUse="viewingConfirmModal" 
      @cancel="viewingConfirmModal = !viewingConfirmModal"
      @yes="deleteItem"
    >
      <template v-slot:title>
        {{ confirmTitle }}
      </template>

      <template v-slot:body>
        {{ confirmBody }}
      </template>
    </ConfirmModal>
  </teleport>

</template>

<script setup>
import { useAlertLoadingTransition, useMainContentTransition, useFormTransition } from '../composables/useTransition';
import { onMounted, ref, computed, onBeforeUnmount } from 'vue';
import { useAlertMessages } from '../composables/useAlertMessages';
import { useConfirmModal } from '../composables/useConfirmModal';
import axiosClient from '../axios';
import SuccessBanner from '../components/alerts/SuccessBanner.vue';
import ErrorBanner from '../components/alerts/ErrorBanner.vue';
import Loading from '../components/alerts/Loading.vue';
import ButtonYellow from '../components/buttons/ButtonYellow.vue';
import ConfirmModal from '../components/ConfirmModal.vue';
import ButtonWhite from '../components/buttons/ButtonWhite.vue';

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

const { 
  viewingConfirmModal, 
  confirmTitle,
  confirmBody,
  confirmId,
  openConfirmModal 
} = useConfirmModal()

const alertLoadingTransition = useAlertLoadingTransition()
const mainContentTransition = useMainContentTransition()
const formTransition = useFormTransition()

const viewingItemsList = ref(null)
const viewingAddForm = ref(null)
const viewingUpdateForm = ref(null)
const pageContent = ref(null)

const currentItemId = ref(null)
const searchValue = ref("")
const loading = ref(false)
const dataStatus = ref(null)
const viewingCategoryMenu = ref(false)
const viewingUnitMenu = ref(false)

const categoriesListData = ref([])
const unitsListData = ref([])
const itemsListData = ref([])

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

const addFormData = ref({
  itemName: null,
  description: null,
  price: null,
  cost: null,
  categoryId: null,
  categoryName: null,
  unitId: null,
  unitName: null,
  reorderLevel: null,
  sku: null
})

const updateFormData = ref({
  id: null,
  itemName: null,
  description: null,
  cost: null,
  price: null,
  categoryId: null,
  categoryName: null,
  unitId: null,
  unitName: null,
  reorderLevel: null,
  sku: null
})

const errors = ref({
  itemName: [],
  price: [],
  cost: [],
  reorderLevel: []
})

const filteredItemsListData = computed(() => {
  return itemsListData.value.filter(item => {
    return item.itemName.toLowerCase().includes(searchValue.value.toLowerCase()) || 
      item.sku.toLowerCase().includes(searchValue.value.toLowerCase())
  })
})

async function submitUpdateForm(id){
  loading.value = true

  try {
    const response = await axiosClient.put(`api/update-item/${id}`, updateFormData.value)
    
    showSuccessMsg("Success", response.data.message)      
    await getItemsListData()
    showItemsList()
  } 
  catch (error) {
    showErrorMsg(error.response?.data?.message || 'Failed to update item') 
    errors.value = error.response?.data?.errors || {}
  }
  finally{
    loading.value = false
  }
}

async function submitAddForm(){
  loading.value = true
  
  try {
    const response = await axiosClient.post('/api/store-item', addFormData.value)
    
    showSuccessMsg("Success", response.data.message)
    await getItemsListData()
    showItemsList()
  } 
  catch (error) {
    showErrorMsg(error.response?.data?.message || 'Failed to add item')         
    errors.value = error.response?.data?.errors || {}
  }
  finally{
    loading.value = false
  }
}

async function deleteItem(){
  loading.value = true

  try {
    const response = await axiosClient.delete(`/api/delete-item/${confirmId.value}`)
    
    viewingConfirmModal.value = false
    showSuccessMsg("Success", response.data.message)
    await getItemsListData()
  } 
  catch (error) {
    showErrorMsg(error.response?.data?.message || "Failed to delete item") 
  }
  finally{
    loading.value = false
  }
}

async function getItemsListData() {
  dataStatus.value = "Loading..."  

  try {
    const response = await axiosClient.get('/api/items-list')
    
    itemsListData.value = response.data.itemsListData
    categoriesListData.value = response.data.categoriesListData
    unitsListData.value = response.data.unitsListData

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
      dataStatus.value = "No data"
    }else{
      dataStatus.value = null
    }
  }
}

function changeContent(content){
  if(content === 'Items List'){
    viewingItemsList.value = true
  }else if(content === 'Add Item'){
    viewingAddForm.value = true
  }else if(content === 'Update Item'){
    viewingUpdateForm.value = true
  }else {
    return
  }

  mainContentTransition.value.enterFrom = "-translate-x-1/2 opacity-0"
}

function showItemsList () {
  viewingAddForm.value = false
  viewingUpdateForm.value = false

  clearInputsErrors()
}

function showAddForm () {
  viewingItemsList.value = false
  pageContent.value = "Add Item"
}

function showUpdateForm (item) {
  viewingItemsList.value = false
  pageContent.value = "Update Item"

  updateFormData.value.id = item.id
  updateFormData.value.itemName = item.itemName
  updateFormData.value.description = item.description
  updateFormData.value.cost = item.cost
  updateFormData.value.price = item.price
  updateFormData.value.categoryId = item.categoryId
  updateFormData.value.categoryName = item.category
  updateFormData.value.unitId = item.unitId
  updateFormData.value.unitName = item.unit
  updateFormData.value.reorderLevel = item.reorderLevel
  updateFormData.value.sku = item.sku
}

function toggleMoreActions (id){
  if (currentItemId.value == id){
    currentItemId.value = null
  }else{
    currentItemId.value = id
  } 
}

function selectCategory(id, name){
  if(id){
    if(viewingAddForm.value){
      addFormData.value.categoryId = id
      addFormData.value.categoryName = name
    }else if(viewingUpdateForm.value){
      updateFormData.value.categoryId = id
      updateFormData.value.categoryName = name
    }
  }else{
    addFormData.value.categoryId = null
    addFormData.value.categoryName = null
    updateFormData.value.categoryId = null
    updateFormData.value.categoryName = null
  }
}

function selectUnit(id, name) {
  if(id){
    if(viewingAddForm.value){
      addFormData.value.unitId = id
      addFormData.value.unitName = name
    }else if(viewingUpdateForm.value){
      updateFormData.value.unitId = id
      updateFormData.value.unitName = name
    }
  }else{
    addFormData.value.unitId = null
    addFormData.value.unitName = null
    updateFormData.value.unitId = null
    updateFormData.value.unitName = null
  }
}
 
function handleClickOutside(e) {
  if (!e.target.closest(".btnActions")) {
    currentItemId.value = null
  }

  if(!e.target.closest('.category-dropdown')){
    viewingCategoryMenu.value = false
  }
  
  if(!e.target.closest('.unit-dropdown')){
    viewingUnitMenu.value = false
  }
}

function clearInputsErrors(){
  errors.value.itemName = []
  errors.value.price = []
  errors.value.cost = []

  addFormData.value.itemName = null
  addFormData.value.description = null
  addFormData.value.price = null
  addFormData.value.cost = null
  addFormData.value.categoryId = null
  addFormData.value.categoryName = null
  addFormData.value.unitId = null
  addFormData.value.unitName = null
  addFormData.value.reorderLevel = null
  addFormData.value.sku = null

  updateFormData.value.id = null
  updateFormData.value.itemName = null
  updateFormData.value.description = null
  updateFormData.value.cost = null
  updateFormData.value.price = null
  updateFormData.value.categoryId = null
  updateFormData.value.categoryName = null
  updateFormData.value.unitId = null
  updateFormData.value.unitName = null
  updateFormData.value.reorderLevel = null
  updateFormData.value.sku = null

  viewingCategoryMenu.value = false
  viewingUnitMenu.value = false
}

onMounted(() => {
  viewingItemsList.value = true
  viewingAddForm.value = false
  viewingUpdateForm.value = false
  getItemsListData()

  document.addEventListener("click", handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside)
})

</script>

<style scoped>

</style>