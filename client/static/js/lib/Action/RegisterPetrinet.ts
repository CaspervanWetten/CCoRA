/// <reference path='./index.ts'/>

class RegisterPetrinet extends RequestingAction
{
    protected FormData : FormData;

    public constructor(fd ?: FormData)
    {
        super();
        if(fd != null) { 
            this.FormData = fd;
        }
    }

    public Invoke()
    {
	let store = Store.GetInstance();
	let data = this.FormData;
	data.set("user_id", store.GetUserId().toString());
        RequestStation.RegisterPetrinet(this, data);
    }
    
    protected PerformSuccess(code: number, responseText: string) {
        let response : PetrinetCreatedResponse = JSON.parse(responseText);
        let store = Store.GetInstance();
        store.SetPetrinetId(response.petrinet_id);
	store.SetMarkingId(response.marking_id);
    }
    protected PerformFailure(code: number, responseText: string) {
        console.warn("Petri net registration failed");
    }
    protected PerformBusy() {
        console.log("registering Petri net");
    }
    public SetFormData(fd : FormData)
    {
        this.FormData = fd;
    }
}
