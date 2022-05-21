export const initState = {
    isLoading: false,
    job: null,
    step: 1,
    isErrors: false,
    errors: [],
    ipLocation: null,
    availablePlaces: [],

    selectedPlace: {
        country: "EG",
        gov: "Cairo Governorate",
    },
    
    userData: {
        f_name: '',
        l_name: '',
        email: '',
        phone: '',
        password: '',
        gender: '',
        lang: '',
        coords: ''
    },

    supplierInfo: {
        shop_name: '',
        whatsapp_no: '',
        fb_page: '',
        website_domain: '',
    },

    availableCustomerCategories: [],

    customerInfo: {
        shop_name: '',
        vat_no: '',
        category_id: ''
    },

    translate: {}
}

export default { ...initState };