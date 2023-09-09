import { defineStore } from "pinia";
import { getAll } from "../api/genreCategory";
import { AxiosResponse } from "axios";
import { GenreCategorySchema } from "../components/GenreCategory/@types/index";

export type State = {
    all?: GenreCategorySchema[],
    selectedGenreCategory: undefined,
}

export const useGenreCategoryStore = defineStore('genreCategory', {
    state: () : State => ({
        all: undefined,
        selectedGenreCategory: undefined,
    }),

    getters: {
    },

    actions: {
        async all() {
            const response : AxiosResponse<GenreCategorySchema[]> = await getAll();
            // this.all = response.data;
        }
    }
});
