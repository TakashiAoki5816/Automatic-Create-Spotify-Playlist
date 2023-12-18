<script setup lang='ts'>
import { reactive } from 'vue';
import { useGenreCategoryStore } from '@/store/GenreCategoryStore';

const genreCategoryStore = useGenreCategoryStore();

const formData = reactive({
    'genres': '',
});
</script>
<template>
    <FormKit
        type="radio"
        label="ジャンル"
        name="genres"
        :options="['J-POP', '洋楽', 'アニソン', 'K-POP']"
        validation="required"
        outer-class="mt-5"
        legend-class="block mb-2 text-sm text-gray-900 dark:text-white"
        wrapper-class="flex items-center mb-4"
        label-class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300"
        input-class="w-4 h-4"
    />
</template>
<style></style>
