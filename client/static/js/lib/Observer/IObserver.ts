/// <reference path='./index.ts'/>

interface IObserver<T extends IObservable>
{
    Update(observable : T);
}
