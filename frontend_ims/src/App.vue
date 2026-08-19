<template>

  <Transition
    :enter-from-class="alertLoadingTransition.enterFrom"
    :enter-to-class="alertLoadingTransition.enterTo"
    :enter-active-class="alertLoadingTransition.enterActive"
    :leave-from-class="alertLoadingTransition.leaveFrom"
    :leave-to-class="alertLoadingTransition.leaveTo"
    :leave-active-class="alertLoadingTransition.leaveActive"
  >
    <Loading v-if="isLoading"></Loading>
  </Transition>

  <RouterView />
  
</template>

<script setup>
import Loading from './components/alerts/Loading.vue';
import { useLoading } from './composables/useLoading';
import { useAlertLoadingTransition } from './composables/useTransition';

const alertLoadingTransition = useAlertLoadingTransition()
const { isLoading } = useLoading()


</script>

<style scoped>

</style>
