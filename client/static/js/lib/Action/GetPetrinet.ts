/// <reference path='./index.ts'/>
/// <reference path='../Response/index.ts'/>

class GetPetrinet extends RequestingAction
{
    public Invoke()
    {
        let store = Store.GetInstance();
        let pid   = store.GetPetrinetId();
        RequestStation.GetPetrinet(this, pid);
    }
    protected PerformSuccess(code:number, responseText:string)
    {
        let response : PetrinetResponse = JSON.parse(responseText);
        let places = response.places;
        let trans  = response.transitions;

        let store = Store.GetInstance();
        let petrinet = new Petrinet(places, trans);
        store.SetPetrinet(petrinet);
    }
    protected PerformFailure(code:number, responseText:string)
    {
        try {
            console.warn("Could not get Petri net"); 
        }
        catch(e) 
        {
            alert(e);
        }
    }
    protected PerformBusy() 
    {
        console.log("getting Petri net...")
    }
}