/// <reference path='./index.ts'/>
/// <reference path='../Workspace/index.ts'/>
/// <reference path='../../../Action/index.ts'/>

abstract class InitAction extends WorkspaceBoundAction<InitWorkspace>
{
    protected SetBusy()
    {
        this.Workspace.Dialog.SetTitle("Loading...");
        let loader = document.createElement("div");
        loader.classList.add("loader");
        this.Workspace.Dialog.SetBody(loader);
    }

    protected SetBody(bodyElements : HTMLElement[])
    {
        if(bodyElements.length > 0) {
            let d = this.Workspace.Dialog;
            d.SetBody(bodyElements[0]);
            for(let i = 1; i < bodyElements.length; i++) {
                d.AppendBody(bodyElements[i]);
            }
        }
    }

    protected SetError(error : ErrorResponse, bodyElements ? : HTMLElement[])
    {
        let e = document.createElement("p");
        e.appendChild(document.createTextNode(error.error));
        e.classList.add("error");
        
        this.Workspace.Dialog.SetTitle("Something went wrong...");
        
        if(bodyElements != null) {
            bodyElements.unshift(e);
            this.SetBody(bodyElements);
        }
        else {
            this.SetBody([e]);
        }
    }
}