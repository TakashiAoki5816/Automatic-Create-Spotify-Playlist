import axios, { AxiosResponse } from 'axios';

export const getAll = async () : Promise<AxiosResponse> => {
    return await axios.get('/api/spotify/genre-categories');
};
