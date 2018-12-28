/// <reference path='./index.ts'/>
/// <reference path='../Actions/index.ts'/>
/// <reference path='../../../Workspace/index.ts'/>

class InitWorkspace extends DialogWorkspace<InitWorkspace>
{
    public constructor()
    {
        super();
        this.Dialog.SetElementClassname("dialog");
        this.Dialog.SetTitle("");
        this.Dialog.SetBody(document.createElement("div"));
        document.body.appendChild(this.Dialog.Render());
        this.SetAction(new InitRegisterUser(this));
    }
}