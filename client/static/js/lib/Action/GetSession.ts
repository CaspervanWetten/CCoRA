/// <reference path='./index.ts'/>
class GetSession extends RequestingAction {
    public Invoke()
    {
        let store = Store.GetInstance();
        if(store.GetPetrinetId() != null && store.GetUserId() != null) {
            let uid = store.GetUserId();
            let pid = store.GetPetrinetId();
            RequestStation.SetSession(this, uid, pid);
        }
    }
    
    protected PerformSuccess(code: number, responseText: string) {
        try{
            let s : SessionResponse = JSON.parse(responseText);
            let store = Store.GetInstance();
            store.SetSessionId(s.session_id);
        }
        catch(e) {
            console.warn(e);
        }
    }
    protected PerformFailure(code: number, responseText: string) {
        try{
            let e : ErrorResponse = JSON.parse(responseText);
            console.log(e);
        }
        catch(e){
            console.warn(e);
        }
    }
    protected PerformBusy() {
        console.log("setting session...");
    }
}