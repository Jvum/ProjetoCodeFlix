import HttpResource from './http-resource';
import httpVideo from './index';
import { AxiosResponse } from 'axios';

const categoryHttp = new HttpResource(httpVideo, "categories");

class CategoryHttp extends HttpResource{
    list(): Promise<AxiosResponse> {
        return super.list();
    }
}

export default categoryHttp;