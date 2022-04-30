import { user as authedUser } from '../../tools/authorization'
import store from '../../auth/store/store'

export default async (to, from, next) => {
    store.commit('TOGGLE_LOADING_STATE')
    const user = await authedUser();
    if (user == null) {
        store.commit('TOGGLE_LOADING_STATE')
        next()
        return
    }

    const userJob = user.job;

    if (userJob === '0')
        window.location.href = window.suppliersDashboardUrl;

    if (userJob === '1' || userJob === '2') 
        window.location.href = 'sellers-app';
        
    if (userJob === '3' || userJob === '5')
    window.location.href = window.customersDashboardUrl;

    if (userJob === '4')
        window.location.href = window.adminsDashboardUrl;

    next();
    return;
}