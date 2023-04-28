// assets/js/store.js

import { createStore } from 'redux';

const initialState = {
    jsonData: null,
};

function reducer(state = initialState, action) {
    switch (action.type) {
        case 'SET_JSON_DATA':
            return {
                ...state,
                jsonData: action.payload,
            };
        default:
            return state;
    }
}

const store = createStore(reducer);

export default store;
