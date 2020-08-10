import { RouteProps } from "react-router-dom";
import Dashboard from "../pages/Dashboard";
import CategoryList from "../pages/category/PageList";
import CastMemberList from '../pages/cast-member/PageList';
import GenreList from '../pages/genre/PageList';

export interface MyRouteProps extends RouteProps {
    name: string;
    label: string;
}

const routes: MyRouteProps[] = [
    {
        name: 'dashboard',
        label: 'Dashboard',
        path: '/',
        component: Dashboard,
        exact: true
    },
    {
        name: 'categories.list',
        label: 'Listar Categorias',
        path: '/categories',
        component:  CategoryList,
        exact: true
    },
    {
        name: 'categories.create',
        label: 'Criar Categorias',
        path: '/categories/create',
        component:  CategoryList,
        exact: true
    },
    {
        name: 'cast-members.list',
        label: 'Listar membros de elencos',
        path: '/cast-members',
        component:  CastMemberList,
        exact: true
    },
    {
        name: 'cast-members.create',
        label: 'Listar membros de elencos',
        path: '/cast-members/create',
        component:  CastMemberList,
        exact: true
    },
    {
        name: 'genres.list',
        label: 'Listar Generos',
        path: '/genres',
        component:  GenreList,
        exact: true
    },
    {
        name: 'genres.create',
        label: 'Listar Generos',
        path: '/genres/create',
        component:  GenreList,
        exact: true
    },
    {
        name: 'categories.edit',
        label: 'Editar Categorias',
        path: '/categories/:id/edit',
        component:  CategoryList,
        exact: true
    },
];

export default routes;