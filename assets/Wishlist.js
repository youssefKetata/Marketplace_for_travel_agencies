import React from "react";
import ReactDOM from "react-dom/client";
import {StrictMode} from "react";
import WhishList from "./react/controllers/whishList";

const myComponent = ReactDOM.createRoot(document.getElementById('whishList'));
myComponent.render(
    <StrictMode>
        <WhishList />
    </StrictMode>
);