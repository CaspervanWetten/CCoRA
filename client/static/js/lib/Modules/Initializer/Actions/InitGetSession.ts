/// <reference path='./index.ts'/>

class InitGetSession extends InitRequestingAction
{
    public Invoke()
    {
        let action = new GetSession();
        action.Attach(this);
        action.Invoke();
    }

    protected PerformBusy() {
        this.SetBusy();
    }
    
    protected PerformSuccess(code: number, responseText: string) {
        try{
            this.Workspace.Dialog.Remove();        
        }
        catch(e) {
            console.warn(e);
        }
    }
    
    protected PerformFailure(code: number, responseText: string) {
        try{
            let e : ErrorResponse = JSON.parse(responseText);
            let p = document.createElement("p");
            p.appendChild(document.createTextNode("Please contact an administrator"));
            this.SetError(e, [p]);
        }
        catch(e){
            console.warn(e);
        }
    }
}