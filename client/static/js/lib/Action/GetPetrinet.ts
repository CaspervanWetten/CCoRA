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
        let response : MarkedPetrinetResponse = JSON.parse(responseText);
	let net = response.petrinet;
	let places = net.places;
	let transitions = net.transitions;

        let store = Store.GetInstance();
        let petrinet = new Petrinet(places, transitions);
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
