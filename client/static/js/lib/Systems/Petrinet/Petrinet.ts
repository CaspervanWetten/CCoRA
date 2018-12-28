/// <reference path='./index.ts'/>
/// <reference path='../State.ts'/>
/// <reference path='../../../vendor/Definitions/Hashset.d.ts'/>

class Petrinet
{
    protected Places            : IHashSet<Place>;
    protected Transitions       : IHashSet<Transition>;
    // protected InitialMarking    : State;

    public constructor(places : string[], transitions : string[]) 
    {
        this.Places = new HashSet<Place>();
        this.Transitions = new HashSet<Transition>();

        for(let i = 0; i < places.length; i++) {
            this.Places.add(places[i]);
        }
        for(let i = 0; i < transitions.length; i++) {
            this.Transitions.add(transitions[i]);
        }
    }

    public GetPlaces()
    {
        return this.Places.values();
    }
    
    public GetTransitions()
    {
        return this.Transitions.values();
    }
}