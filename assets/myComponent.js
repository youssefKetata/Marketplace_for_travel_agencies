import React from "react";
import ReactDOM from "react-dom/client";
import {StrictMode} from "react";
import MyComponent from "./react/controllers/myComponent";
import NavBar from "./react/controllers/NavBar";

const myComponent = ReactDOM.createRoot(document.getElementById('myComponent'));
myComponent.render(
    <StrictMode>
        <NavBar />

    </StrictMode>
);
