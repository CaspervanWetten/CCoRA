/// <reference path='../Observer/index.ts'/>
/// <reference path='../Systems/index.ts'/>
/// <reference path='../Utils/Extensions/Array.ts'/>

interface StoreObserver extends IObserver<Store> {
    Update(observable : Store);
}

class Store implements IObservable
{
    protected static Instance   : Store;
    protected Observers         : StoreObserver[];

    protected Graph     : Graph;
    protected Petrinet  : Petrinet;
    protected UserId    : number | undefined;
    protected PetrinetId: number | undefined;
    protected SessionId : number | undefined;
    
    protected constructor()
    {
        this.Observers = [];
    }
    
    public Init()
    {
        this.UserId     = undefined;
        this.PetrinetId = undefined;
        this.SessionId  = undefined;

        this.Graph = new Graph();
        this.Notify();
    }

    public static GetInstance()
    {
        if(!this.Instance) {
            this.Instance = new Store();
        }
        return this.Instance;
    }

    public Attach(observer : StoreObserver)
    {
        this.Observers.push(observer);
    }

    public Detach(observer : StoreObserver)
    {
        this.Observers.removeAt(this.Observers.indexOf(observer));
    }

    public Notify()
    {
        for(let i = 0; i < this.Observers.length; i++) {
            this.Observers[i].Update(this);
        }
    }

    public GetGraph()
    {
        return this.Graph;
    }

    public GetUserId()
    {
        return this.UserId;
    }

    public SetUserId(id : number)
    {
        this.UserId = id;
        this.Notify();
    }

    public GetPetrinetId()
    {
        return this.PetrinetId;
    }

    public SetPetrinetId(id : number)
    {
        this.PetrinetId = id;
        this.Notify();
    }

    public GetSessionId()
    {
        return this.SessionId;
    }

    public SetSessionId(id : number)
    {
        this.SessionId = id;
        this.Notify();
    }

    public GetPetrinet()
    {
        return this.Petrinet;
    }

    public SetPetrinet(p : Petrinet)
    {
        this.Petrinet = p;
        this.Notify();
    }
}