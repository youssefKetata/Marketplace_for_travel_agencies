import React from 'react';

export function CalcColum(column) {
    let total = 0;
    for (let i = 0; i < column.length; i++) {
        if (Number.isInteger(column[i])) {
            total += column[i].value;
        }else{
            return false
        }
        return total;
    }
}