<script setup lang='ts'>
import { reactive } from 'vue';
import { useGenreCategoryStore } from '../../store/GenreCategoryStore';

const genreCategoryStore = useGenreCategoryStore();

const formData = reactive({
    'genres': [],
});
</script>
<template>
    <FormKit
        type="checkbox"
        label="ジャンル"
        name="genres"
        :options="['Mushrooms', 'Olives', 'Salami', 'Anchovies']"
        validation="required|min:1"
        outer-class="mt-5"
        legend-class="block mb-2 text-sm text-gray-900 dark:text-white"
        wrapper-class="flex items-center mb-4"
        label-class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300"
        input-class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
    />
</template>
<style></style>
