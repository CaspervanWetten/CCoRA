/// <reference path='./index.ts'/>
/// <reference path='../../../Action/index.ts'/>
class InitGetPetrinet extends InitRequestingAction 
{
    public Invoke()
    {
        let action = new GetPetrinet();
        action.Attach(this);
        action.Invoke();
    }

    protected PerformBusy() {
        this.SetBusy();
    }
    
    protected PerformSuccess(code: number, responseText: string) {
        let nextAction = new InitGetSession(this.Workspace);
        this.Workspace.SetAction(nextAction);
    }
    
    protected PerformFailure(code: number, responseText: string) {
        let response : ErrorResponse = JSON.parse(responseText);
        let body = this.GetErrorBody();
        this.SetError(response, body);
    }

    protected GetErrorBody(): HTMLElement[] {
        let p = document.createElement("p");
        p.appendChild(document.createTextNode("The system could not fetch the petrinet. "));
        p.appendChild(document.createTextNode("Contact an administator"));
        return [p];
    }
}
