/// <reference path='./index.ts'/>
/// <reference path='../../../Factories/FormFactory.ts'/>
/// <reference path='../../../ResponseInterpreter/index.ts'/>

class InitRegisterPetrinetAction extends InitRequestingAction
{
    public Invoke() {
        this.Workspace.Dialog.SetTitle("Alright. Now the Petri net!");
        let body = this.GetBody();
        this.SetBody(body);
    }
    
    protected SendForm(fd : FormData)
    {
        let action = new RegisterPetrinet(fd);
        action.Attach(this);
        action.Invoke();
    }

    protected PerformBusy() {
        this.SetBusy();
    }
    
    protected PerformSuccess(code: number, responseText: string) {
        let nextAction = new InitGetPetrinet(this.Workspace);
        this.Workspace.SetAction(nextAction);
    }
    
    protected PerformFailure(code: number, responseText: string) {
        let error : ErrorResponse = JSON.parse(responseText);
        this.SetError(error, this.GetBody());
    }
    
    protected GetBody()
    {
        let p = document.createElement("p");
        p.appendChild(document.createTextNode(
            "You are now succesfully registered. Upload a Petri net so we can get started."
        ));

        let form = FormFactory.GetPetrinetRegistrationForm();

        let button = document.createElement("button");
        button.classList.add("confirm");
        button.appendChild(document.createTextNode(
            "Upload"
        ));
        button.addEventListener("click", (e)=>{
            this.SendForm(new FormData(form));
        });

        let res = [p, form, button];
        return res;
    }
}
