
<template>
  <PageComponent>
    <template v-slot:header>
      <h1 class="text-3xl font-bold tracking-tight text-gray-900">Dashboard</h1>
    </template>
    <div v-if="loading" class="flex justify-center">Loading...</div>
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 text-gray-700">
      <div class="bg-white shadow-md p-3 text-center flex flex-col order-1 lg:order-2 animate-fade-in-down "
        style="animation-delay:0.1s ;">
        <h3 class="text-2xl font-semibold">Total surveys</h3>
        <div class="text-8xl font-semibold flex-1 flex items-center justify-center">
          {{ data.totalSurveys }}
        </div>
      </div>
      <div class="bg-white shadow-md p-3 text-center flex flex-col order-2 lg:order-4 animate-fade-in-down"
        style="animation-delay:0.2s ;">
        <h3 class="text-2xl font-semibold">Total Answers</h3>
        <div class="text-8xl font-semibold flex-1 flex items-center justify-center">
          {{ data.totalAnswers }}
        </div>
      </div>
      <div class="bg-white shadow-md p-4 text-center row-span-2 order-3 lg:order-1 animate-fade-in-down"
        style="animation-delay:0.2s ;">
        <h3 class="text-2xl font-semibold">Latest Survey</h3>
        <img :src="data.latestSurvey?.image_url" class="w-[240px] mx-auto" alt="" />
        <h3 class="font-bold text-xl text-left mb-3">{{ data.latestSurvey?.title }}</h3>
        <div class="flex justify-between text-sm mb-1">
          <div>Upload Date : </div>
          <div>{{ data.latestSurvey?.created_at }}</div>
        </div>
        <div class="flex justify-between text-sm mb-1">
          <div>Expire Date : </div>
          <div>{{ data.latestSurvey?.expire_date }}</div>
        </div>
        <div class="flex justify-between text-sm mb-1">
          <div>Status : </div>
          <pre>{{ data.latestSurvey?.status }}</pre>
          <div>{{ data.latestSurvey?.status ? 'Active' : 'Draft' }}</div>
        </div>
        <div class="flex justify-between text-sm mb-3">
          <div>Question : </div>
          {{ data.latestSurvey?.questions }}
        </div>
        <div class="flex justify-between text-sm mb-3">
          <div>Answers : </div>
          {{ data.latestSurvey?.answers }}
        </div>
        <div class="flex justify-between">

          <router-link :to="{ name: 'SurveyView', params: { id: data.latestSurvey?data.latestSurvey.id:0 } }"
            class="flex py-2 px-3 border border-indigo-300 text-sm rounded-md text-indigo-500 hover:bg-indigo-700 hover:text-white transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
            </svg>
            Edit Survey
          </router-link>
          <button
            class=" flex py-2 px-4 border border-indigo-300 text-sm rounded-md text-indigo-500 hover:bg-indigo-700 hover:text-white transparent-colors focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            View Answers
          </button>
        </div>
      </div>
      <div class="bg-white shadow-md p-3 text-left row-span-2 order-4 lg:order-3 animate-fade-in-down"
        style="animation-delay:0.3s ;">
        <div class="flex justify-between items-center mb-3 px-2">
          <h3 class="text-2xl font-semibold">Latest Answers</h3>
          <a class="text-sm text-blue-500 hover:decoration-blue-500" href="javascript:void(0)">
            View All
          </a>
        </div>
        <a href="#" v-for="answer of data.latestAnswers" :key="answer.id" class="block p-2 hover:bg-gray-100/90">
          <div class="font-semibold">{{ answer.survey.title }}</div>
          <small>
            Answer Made At:
            <i class="font-semibold">{{ answer.end_date }}</i>
          </small>
        </a>
      </div>
    </div>
  </PageComponent>
</template>

<style scoped></style>

<script setup>
import { useStore } from "vuex";
import PageComponent from "../components/PageComponent.vue";
import { computed } from "vue";

const store = useStore();
const loading = computed(() => store.state.dashboard.loading);
const data = computed(() => store.state.dashboard.data);
store.dispatch('getDashboardData')
</script>
