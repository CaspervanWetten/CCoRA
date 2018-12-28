/// <reference path='./index.ts'/>

/// <reference path='../../Action/index.ts'/>
/// <reference path='../../Workspace/index.ts'/>
/// <reference path='../../Models/Store.ts'/>

class Initializer implements StoreObserver
{
    public Update(store : Store)
    {
        // If we do not know which user we have, we need to find out
        // by letting him/her register.
        if(!store.GetUserId()){
            let l = new InitWorkspace();
            l.Work();
        }

        // Once the user has selected a petrinet we need to set up the
        // modelling environment.
        if(store.GetPetrinetId() != null 
        && store.GetPetrinet() != null 
        && store.GetSessionId() != null ) {
            let container = document.getElementById("modeller");
            let m = new ModellerWorkspace(container);
            m.Work();
        }
    }
}