<script setup lang='ts'>
interface Props {
    label: string;
    name: string;
    validation?: string,
}

const props = defineProps<Props>();
</script>
<template>
    <FormKit
        type="text"
        :label=label
        :name=name
        :validation=validation
        label-class="block mt-20 mb-2 text-sm text-gray-900 dark:text-white"
        input-class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light"
    />
</template>
