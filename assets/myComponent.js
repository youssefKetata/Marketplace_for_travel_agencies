import React from "react";
import ReactDOM from "react-dom/client";
import {StrictMode} from "react";
import App from "./react/controllers/App";

const myComponent = ReactDOM.createRoot(document.getElementById('myComponent'));
myComponent.render(
    <StrictMode>
        <App />
    </StrictMode>
);
