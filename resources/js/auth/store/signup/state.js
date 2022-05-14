export const initState = {
    isLoading: false,
    job: '0',
    step: 1,
    isErrors: false,
    errors: [],
    ipLocation: null,
    availablePlaces: [],
    userData: {
        f_name: '',
        f_name_ar: '',
        l_name: '',
        l_name_ar: '',
        email: '',
        username: '',
        phone: '',
        password: '',
        country: '',
        city: '',
        gender: '',
        lang: '',
    },

    supplierInfo: {
        vat_no: '',
        shop_name: '',
        whatsapp_no: '',
        fb_page: '',
        website_domain: '',
        location_coords: '',
        l1_address: '',
        l1_address_ar: '',
        l2_address: '',
        l2_address_ar: '',
    },

    availableCustomerCategories: [],

    customerInfo: {
        shop_name: '',
        l1_address: '',
        l1_address_ar: '',
        l2_address: '',
        l2_address_ar: '',
        location_coords: '',
        vat_no: '',
        category_id: '',
        shop_space: '',
    },

    OnlineClientInfo: {}
}

export default { ...initState };