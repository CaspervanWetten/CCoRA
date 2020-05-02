/// <reference path='./index.ts'/>

class RegisterUser extends RequestingAction
{
    protected FormData : FormData;

    public constructor(fd : FormData = null)
    {
        super();
        this.FormData = fd;
    }

    public Invoke()
    {
        if(this.FormData != null) {
            RequestStation.RegisterUser(this, this.FormData);
        }
        else {
            console.log("Could not register user: parameter unknown");
        }
    }

    public PerformSuccess(code:number, responseText:string)
    {
        try{
            let response : UserCreatedResponse = JSON.parse(responseText);
            let store = Store.GetInstance();
            store.SetUserId(response.user_id);
        }
        catch(e) {
            console.log(responseText);            
        }
    }

    public PerformFailure(code:number, responseText:string)
    {
        try{
            console.warn("registration failed");
        }
        catch(e) {
            alert(e);
        }
    }

    public PerformBusy()
    {
        console.log("registering user...");
    }

    public SetFormData(fd : FormData)
    {
        this.FormData = fd;
    }
}
