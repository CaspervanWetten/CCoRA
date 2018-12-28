/// <reference path='./index.ts'/>
/// <reference path='../../../Action/index.ts'/>

class InitRegisterUser extends InitRequestingAction
{
    public Invoke() {
        let d = this.Workspace.Dialog;
        d.SetTitle("Welcome");
        let body = this.GetBody();
        this.SetBody(body);
    }

    protected SendForm(fd : FormData)
    {
        let action = new RegisterUser(fd);
        action.Attach(this);
        action.Invoke();
    }

    protected PerformBusy()
    {
        this.SetBusy();
    }

    protected PerformSuccess(code: number, responseText: string) 
    {
        try{
            let nextAction = new InitRegisterPetrinetAction(this.Workspace);
            this.Workspace.SetAction(nextAction);
        }
        catch(e) {
            console.log(responseText);
        }
    }

    protected PerformFailure(code: number, responseText: string) {
        try{
            let response : ErrorResponse = JSON.parse(responseText);
            let body = this.GetBody();
            this.SetError(response, body);
        }
        catch(e) {
            console.log(responseText);
        }
    }

    protected GetBody()
    {
        let p = document.createElement("p");
        p.appendChild(document.createTextNode(
            "In order to use this website, please pick a unique username."
        ));

        let form = FormFactory.GetUserRegistrationForm();

        let button = document.createElement("button");
        button.classList.add("confirm");
        button.appendChild(document.createTextNode(
            "Continue"
        ));
        button.addEventListener("click", (e)=>{
            this.SendForm(new FormData(form));
        });

        let res = [p, form, button];
        return res;
    }
}