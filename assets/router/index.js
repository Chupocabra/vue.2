import {createRouter, createWebHashHistory} from 'vue-router'
import {defineComponent} from "vue";
import ResumeList from '../components/ResumeList'
import ResumeDisplay from "../components/ResumeDisplay";


const routes = [
    {
        path: '/',
        name: 'ResumeList',
        component: defineComponent(ResumeList),
    },
    {
        path: '/add',
        name: 'ResumeDisplay',
        component: defineComponent(ResumeDisplay),
    },
    {
        path: '/edit/:id',
        name: 'edit',
        component: defineComponent(ResumeDisplay),
        props: true,
    }
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});

export default router;