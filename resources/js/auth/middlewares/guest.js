import { user as authedUser } from '../../tools/authorization'
import store from '../../auth/store/store'

export default async (to, from, next) => {
    store.commit('TOGGLE_LOADING_STATE')
    const user = await authedUser();
    if (user == null) {
        store.commit('TOGGLE_LOADING_STATE')
        next()
        return;
    }

    const userJob = user.job;
    switch (userJob) {
        case 0:
            window.location.href = window.suppliersDashboardUrl;
            break;

        case 1:
            window.location.href = 'sellers-app';
            break;

        case 2:
            window.location.href = 'sellers-app';
            break;

        case 3:
            window.location.href = window.customersDashboardUrl;
            break;

        case 4:
            window.location.href = window.adminsDashboardUrl;
            break;

        case 5:
            window.location.href = window.customersDashboardUrl;
            break;
    }
    return;
}