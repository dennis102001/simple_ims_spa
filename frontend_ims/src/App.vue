<template>
  <teleport to="body">
    
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
  </teleport>

  <RouterView />
  
</template>

<script setup>
import Loading from './components/alerts/Loading.vue';
import SuccessBanner from './components/alerts/SuccessBanner.vue';
import ErrorBanner from './components/alerts/ErrorBanner.vue';
import { useLoading } from './composables/useLoading';
import { useAlertLoadingTransition } from './composables/useTransition';
import { useAlertMessages } from './composables/useAlertMessages';

const alertLoadingTransition = useAlertLoadingTransition()
const { isLoading } = useLoading()
const { 
  hasSuccessMsg, 
  successMsgTitle, 
  successMsgBody, 
  closeSuccessMsg, 
  hasErrorMsg, 
  errorMsgBody,
  closeErrorMsg, 
} = useAlertMessages()


</script>

<style scoped>

</style>
