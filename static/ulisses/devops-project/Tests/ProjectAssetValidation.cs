using System.Collections.Generic;
using System.Linq;
using NUnit.Framework;
using Ulisses.Core.Utilities.Editor;
using UnityEditor.PackageManager;

namespace Ulisses.Project.Tests {
    [TestFixture]
    internal sealed class ProjectAssetValidation : AssetValidationBase<ProjectAssetValidation.AssetSource> {
        internal sealed class AssetSource : AssetSourceBase {
            protected override string ValidateAssetsInDirectory => "Assets";
            protected override IEnumerable<string> CheckCircularDependenciesForPackages => PackageInfo
                .GetAllRegisteredPackages()
                .Where(p => p.isDirectDependency)
                .Select(p => p.name);
        }
    }
}