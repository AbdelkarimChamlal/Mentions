const dragable = (event) => {
    event.preventDefault();
}

const drag = (event) => {
    event.dataTransfer.setData("drag-identifier", event.target.id);
}

const drop = (event) => {
    event.preventDefault();
    const data = event.dataTransfer.getData("drag-identifier");
    if(event.target.classList.contains("pipe-items")){
        event.target.appendChild(document.getElementById(data));
    } else {
        if(event.target.parentElement.classList.contains("pipe-items")){
            event.target.parentElement.appendChild(document.getElementById(data));
        }else{
            if(event.target.parentElement.parentElement.classList.contains("pipe-items")){
                event.target.parentElement.parentElement.appendChild(document.getElementById(data));
            }else{
                if(event.target.parentElement.parentElement.parentElement.classList.contains("pipe-items")){
                    event.target.parentElement.parentElement.parentElement.appendChild(document.getElementById(data));
                }
            }
        }
    }
    
    
    // else {
    //     event.target.parentElement.appendChild(document.getElementById(data));
    // }
}